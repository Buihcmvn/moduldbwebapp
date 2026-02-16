<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\HeaderService;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MeilensteinController extends AbstractController
{
    public function __construct(
        private readonly HeaderService      $headerService,
        private readonly UserService        $userService,
    ){}
    #[Route('/meilenstein', name: 'meilenstein')]
    public function listMeilenstein(Request $request): Response
    {

        // Abrufen des aktuell authentifizierten Benutzers
        $user           = $this->getUser();
        $userInfor      = $this->userService->inforUser($user);
        $currentRoleId  = intval($request->getSession()->get('currentRoleId'));

        // Combine all data into a single array
        $data = array_merge($userInfor, $this->headerService::ICONS_PATH_HARDWARE, [
            'task'           => 'meilenstein',
            'pathName'       => $request->attributes->get('_route'),
            'currentRoleId'  => $currentRoleId,
        ]);

        return $this->render('Meilenstein/meilenstein.html.twig', $data);
    }

}
