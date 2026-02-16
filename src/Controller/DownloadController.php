<?php
declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;

class DownloadController extends AbstractController
{
    const UPLOAD_FILE_PATH = '/data/uploads/hardware';
    #[Route("/download/{filename}", name: "download")]
    public function downloadFile(string $filename): BinaryFileResponse
    {
        // FilePath to the file download
        $filePath = $this->getParameter('kernel.project_dir') .$this::UPLOAD_FILE_PATH. '/' . $filename;

        // check if file exists
        if (!file_exists($filePath)) {
            throw $this->createNotFoundException('File not found');
        }

        // create BinaryFileResponse
        $response = new BinaryFileResponse($filePath);

        // setup header Content-Disposition for browser to show download window
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $filename
        );

        // Hoặc, nếu bạn muốn hiển thị file trực tiếp trong trình duyệt (ví dụ: PDF)
        // $response->setContentDisposition(
        //     ResponseHeaderBag::DISPOSITION_INLINE,
        //     $filename
        // );

        // Trả về response
        return $response;
    }
}