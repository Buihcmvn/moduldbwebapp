<?php

namespace App\Controller;

use App\Entity\Area;
use App\Repository\AreaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class AreaController extends AbstractController
{
    #[Route('/api/area/get', name: 'api_area_get')]
    public function showListArea(AreaRepository $areaRepository):JsonResponse
    {
        $areaList = $areaRepository->findAll();

        $data = [];
        foreach ($areaList as $area) {
            $data[] = [
                'id' => $area->getId(),
                'name' => $area->getName(),
            ];
        }
        return new JsonResponse($data);
    }

}