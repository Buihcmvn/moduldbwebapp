<?php
declare(strict_types=1);

namespace App\Controller;

use App\Repository\HardwareRepository;
use App\Service\FileService;
use App\Service\HardwareService;
use App\Service\HeaderService;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RechnungController extends AbstractController
{
    public function __construct(
        private readonly HeaderService      $headerService,
        private readonly HardwareService    $hardwareService,
        private readonly FileService        $fileService,
        private readonly UserService        $userService,
        private readonly HardwareRepository $hardwareRepository,
    ){}
    #[Route('/rechnung/list', name: 'rechnung_list')]
    public function listRechnung(Request $request): Response
    {

        // Abrufen des aktuell authentifizierten Benutzers
        $user           = $this->getUser();
        $userInfor      = $this->userService->inforUser($user);
        $currentRoleId  = intval($request->getSession()->get('currentRoleId'));

        // Combine all data into a single array
        $data = array_merge($userInfor, $this->headerService::ICONS_PATH_HARDWARE, [
            'task'           => 'rechnung_list',
            'pathName'       => $request->attributes->get('_route'),
            'currentRoleId'  => $currentRoleId,
        ]);

        return $this->render('Rechnung/rechnung.html.twig', $data);
    }

}
