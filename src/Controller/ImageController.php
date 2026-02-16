<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;

class ImageController extends AbstractController
{
    const UPLOAD_IMAGE_PATH = '/data/uploads/images';
    #[Route('/images/{filename}', name: 'image_display')]
    public function display_img(string $filename): Response
    {
        // Path to save image in server (ex: data/uploads/images)
        $uploadDirectory = $this->getParameter('kernel.project_dir') . $this::UPLOAD_IMAGE_PATH;

        // Full Path
        $filePath = $uploadDirectory . '/' . $filename;

        // check if it exists
        if (!file_exists($filePath)) {
            throw $this->createNotFoundException('Image not found');
        }

        // create a BinaryFileResponse to return a file
        $response = new BinaryFileResponse($filePath);

        // setup disposition (attachment or inline)
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_INLINE,  // showing direct in browser
            $filename
        );
        return $response;
    }
}