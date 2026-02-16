<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\File;
use App\Entity\Hardware;
use App\Entity\Images;
use App\Service\HeaderService;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface; // Use the interface
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class FileService
{
    private FlashBagInterface $flashBag;  // Define $flashBag property as interface
    private HeaderService $headerService;

    public SluggerInterface $slugger;
    private $authorizationChecker;

    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker,
        RequestStack $requestStack,
        HeaderService $headerService,
        SluggerInterface $slugger,
        #[Autowire(param: 'hardware_upload_dir')]
        private string $upLoadHardwareFileDir,
        #[Autowire(param: 'image_upload_dir')]
        private string $upLoadImagesDir
    )
    {
        $this->flashBag = $requestStack->getSession()->getFlashBag();
        $this->headerService = $headerService;
        $this->slugger = $slugger;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function addFlash(string $type, string $message): void
    {
        $this->flashBag->add($type, $message);
    }

    public function uploadFileHardware($file, Hardware $hardware):bool
    {
        // check access
        if (!$this->authorizationChecker->isGranted('SERVICE_ACCESS', $this)) {
            throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException('Access denied.');
        }

        // check File
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = uniqid().'-'.$safeFilename.'.'.$file->guessExtension();

        // Limit size of file
        $maxFileSize = 1024 * 1024; // 1MB
        if ($file->getSize() > $maxFileSize) {
            $this->addFlash('error', 'File size exceeds the limit.');
            return false;
        }

        // check type of file
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'application/pdf'];
        if (!in_array($file->getMimeType(), $allowedMimeTypes)) {
            $this->addFlash('error', 'Invalid file type.');
            return false;
        }
        try {
            $file->move($this->upLoadHardwareFileDir, $fileName);
        } catch (FileException $e) {
            $this->addFlash('error', 'Error uploading file.');
            return false;
        }

        // create encrypted FilePath
        $encryptedFileName = base64_encode($this->upLoadHardwareFileDir.'/'.$fileName);

        // save File in database
        $fileEntity = new File();
        $fileEntity->setHardware($hardware);
        $fileEntity->setEncryptedPath($encryptedFileName);
        $this->headerService->entityManager->persist($fileEntity);
        $this->headerService->entityManager->flush();
        return true;
    }

    public function uploadImgHardware($image, Hardware $hardware):?Images
    {
        // check access
        if (!$this->authorizationChecker->isGranted('SERVICE_ACCESS', $this)) {
            throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException('Access denied.');
        }

        // check File
        $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = uniqid().'-'.$safeFilename.'.'.$image->guessExtension();

        // Limit size of file
        $maxImgSize = (1024 * 1024)*5; // 5MB
        if ($image->getSize() > $maxImgSize) {
            $this->addFlash('error', 'File size exceeds the limit.');
            return null;
        }

        // check type of file
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/svg'];
        if (!in_array($image->getMimeType(), $allowedMimeTypes)) {
            $this->addFlash('error', 'Invalid file type.');
            return null;
        }
        try {
            $image->move($this->upLoadImagesDir, $fileName);
        } catch (FileException $e) {
            $this->addFlash('error', 'Error uploading file.');
            return null;
        }

        // create encrypted FilePath
//        $encryptedFileName = base64_encode($this->upLoadImagesDir.'/'.$fileName);
        $encryptedFileName =$this->upLoadImagesDir.'/'.$fileName;
//        $encryptedFileName = str_replace('C:\Uebung_Cadb/public','',$this->upLoadImagesDir.'/'.$fileName);

        // save File in database
        $imgEntity = new Images();
        $imgEntity->setName(pathinfo($fileName)['filename'].'.'.pathinfo($fileName)['extension'])
            ->setErweiterung(pathinfo($fileName)['extension'])
            ->setDateipfad($encryptedFileName)
            ->setKategorie('image')
            ->setHardware($hardware);

        $this->headerService->entityManager->persist($imgEntity);
        $this->headerService->entityManager->flush();
        return $imgEntity;
    }
}