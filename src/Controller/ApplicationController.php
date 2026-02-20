<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Area;
use App\Entity\Hardware;
use App\Entity\Projekte;
use App\Entity\Software;
use App\Form\ProjektType;
use App\Repository\ProjekteRepository;
use App\Service\HeaderService;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ApplicationController extends AbstractController
{
    const DAFAULT_LENGTH = 5;

    public function __construct(
        private readonly HeaderService          $headerService,
        private readonly EntityManagerInterface $entityManager,
        private readonly ProjekteRepository     $projekteRepository,
        private readonly UserService            $userService,
    ){}


    #[Route('/application/list/{limitPPage}/{page}/{search}',
        name: 'application_list',
        requirements: [ // wenn man hier req definiert --> ERROR Meldung  ???
            'limitPPage'    => '\d+',
            'page'          => '\d+',
            'search'        => '.+',
        ]
    )]
    public function list(
        Request $request,
        PaginatorInterface $paginator,
        int $limitPPage = self::DAFAULT_LENGTH,
        int $page       = 1,
        string $search  = '',
    ): Response
    {
        $properties = (new ReflectionClass (Projekte::class))->getProperties();
        $sort = 'up';
        $sortField = 'id';

        $data = $request->attributes->get('_route_params');

        $queryBuilder = $this->projekteRepository->filterAndSort($search, 'name', $sort, $sortField);
        $sortPagination = $this->headerService->sort_pagination(
            $queryBuilder,
            $limitPPage,
            $page,
            $search,
            $paginator
        );
//        dump($properties, $data, (($sortPagination['list']->getItems())[0])->getArea());

        // Abrufen des aktuell authentifizierten Benutzers
        $user           = $this->getUser();
        $userInfor      = $this->userService->inforUser($user);
        $currentRoleId  = intval($request->getSession()->get('currentRoleId'));

        // dd($userInfor);

        //GET-Daten in der Session speichern
        $request->getSession()->set('currentParams', $request->attributes->get('_route_params'));
        // POST-Daten in der Session speichern
//        $request->getSession()->set('application_sort_direction', $sort);
//        $request->getSession()->set('application_sort_field', $sortField);

        // Combine all data into a single array
        $data = array_merge($userInfor, $sortPagination, $this->headerService::ICONS_PATH_, [
            'task'                      => 'projekt_list',
            'pathName'                  => $request->attributes->get('_route'),
            'properties'                => $properties,
            'sort_field'                => $sortField,
            'currentRoleId'             => $currentRoleId,
        ]);

        // dd($data);

        return $this->render('Application/application.html.twig', $data);
    }


    #[Route('/application/new', name: 'projekte_new')]
    public function new(Request $request):Response
    {
        $projekte = new Projekte();
        $form = $this->createForm(ProjektType::class, $projekte);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Speichern Sie die Software-Entität in der Datenbank
            $this->entityManager->persist($projekte);
            $this->entityManager->flush();

            return $this->redirectToRoute('application_list'); // Beispielroute nach dem Speichern
        }

        // Abrufen des aktuell authentifizierten Benutzers
        $userToken      = $this->getUser();
        $userRole       = $userToken->getRoles();
        return $this->render('Application/projekt_new.html.twig',
            [
                'task'              => 'projekt_new',
                'user'              => $userToken,
                'userRole'          => $userRole,
                'source_symbol_img' => '/'.$this->headerService::ICONS_PATH['logo'],
                'source_user_img'   => '/'.$this->headerService::ICONS_PATH['user'],
                'form'=>$form
            ]);
    }


    #[Route('/application/edit/{id}', name: 'projekte_edit')]
    public function edit(Projekte $projekte, Request $request):Response
    {
        // Abrufen des aktuell authentifizierten Benutzers
        $userToken      = $this->getUser();
        $userRole       = $userToken->getRoles();

        return $this->render('Application/projekt_edit.html.twig',
            [
                'task'              => 'projekt_edit',
                'user'              => $userToken,
                'userRole'          => $userRole,
                'projekt'           => $projekte,
                'source_symbol_img' => '/'.$this->headerService::ICONS_PATH['logo'],
                'source_user_img'   => '/'.$this->headerService::ICONS_PATH['user'],
            ]);
    }


    #[Route('/about', name: 'about')]
    public function about():Response
    {
        // Abrufen des aktuell authentifizierten Benutzers
        $userToken      = $this->getUser();
        $userRole       = $userToken->getRoles();

        return $this->render('About/about.html.twig',
            [
                'user'              => $userToken,
                'userRole'          => $userRole,

                'source_symbol_img' => '/'.$this->headerService::ICONS_PATH['logo'],
                'source_user_img'   => '/'.$this->headerService::ICONS_PATH['user'],
            ]);
    }

}
