<?php
declare(strict_types=1);

namespace App\Controller\Ajax;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\HardwareService;

class AjaxController extends AbstractController
{
    #[Route('/ajax/hardware/api/delete/{id}', name: 'ajax_hardware_delete')]
    public function deleteHardware($id, HardwareService $hardwareService,Request $request): JsonResponse
    {
        try {
            $hardwareService->deleteHardware($id);

            return new JsonResponse(['success' => true]);
        }catch (\Exception $e){
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}