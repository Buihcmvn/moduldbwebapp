<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\HeaderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    private HeaderService $headerService;

    public function __construct(
        HeaderService $headerService,
    )
    {
        $this->headerService = $headerService;
    }

    // TEST --> client site
    public function index(Request $request): Response
    {
        $userToken      = $this->getUser();
        $userRole       = $userToken->getRoles();

        return $this->render('home/index.html.twig', [
            'userID'            => $userToken->getId(),
            'userRole'          => $userRole,
        ]);
    }



    // server site --->
//    #[Route('/home', name: 'home')] // only limit for home page ?? what about another page ???
    public function home(Request $request):Response
    {
        $userToken      = $this->getUser();
        $userRole       = $userToken->getRoles();

        // suchen nach aktuelle Role --> um die aktuelle Recht zu finden
        // default Role ist die, die höher Recht hat (kleiner role ID)
        // Holen Sie sich die Schlüssel des Arrays
        $keys           = array_keys($userRole);
        // Finden Sie den kleinsten Schlüssel
        $smallestKey    = min($keys);
        $defaultRole    = $userRole[$smallestKey];

        // push the currentRoleId value to session
        $request->getSession()->set('currentRoleId', $defaultRole);

//        dd($userRole);

        return $this->render('Home/home.html.twig',[
            'task'              => 'home',
            'user'              => $userToken,
            'userRole'          => $userRole,
            'defaultRole'       => $defaultRole,
            'source_symbol_img' => $this->headerService::ICONS_PATH['logo'],
            'source_user_img'   => $this->headerService::ICONS_PATH['user'],
            'alt_symbol_img'    => $this->headerService::ICONS_PATH['logo']
        ]);
    }




}
