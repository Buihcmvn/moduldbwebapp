<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Software;
use App\Form\SoftwareType;
use App\Repository\SoftwareRepository;
use App\Service\HeaderService;
use Knp\Component\Pager\PaginatorInterface;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SoftwareController extends AbstractController
{
    const DAFAULT_LENGTH = 5;

    public function __construct(
        private readonly HeaderService          $headerService,
        private readonly SoftwareRepository     $softwareRepository,
    ){}




    #[Route('/phpinfor', name: 'phpinfor', methods: ['GET'])]
    public function index(): void
    {
        phpinfo();
//        xdebug_info();
    }

    #[Route('/api/software/list/{limitPPage}/{page}/{sort}/{sortField}/{search}',
        name: 'api_software_list',
        requirements: [ // wenn man hier req definiert --> ERROR Meldung  ???
            'limitPPage'    => '\d+',
            'page'          => '\d+',
            'search'        => '.+',
        ]
    )]
    public function getSoftwares(
        PaginatorInterface $paginator,
        int $limitPPage = self::DAFAULT_LENGTH,
        int $page       = 1,
        string $sort = 'up',
        string $sortField = 'id',
        string $search  = '',
    ): JsonResponse
    {
//        dd($sort,$sortField,$search);
        $queryBuilder = $this->softwareRepository->filterAndSort($search, 'name', $sort, $sortField);
        $sortPagination = $this->headerService->sort_pagination_json(
            $queryBuilder,
            $limitPPage,
            $page,
            $search,
            $paginator
        );
        return new JsonResponse($sortPagination);
    }


    #[Route('/software/list/{limitPPage}/{page}/{search}',
        name: 'software_list',
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
        $properties = (new ReflectionClass(Software::class))->getProperties(); // Anzahl der Eigenschaften zählen
//        $routeParameters    = ['search' => $search, 'maxResult' => $limitPPage];
        // Versuche, den Sortierparameter aus der Anfrage zu erhalten.
        // Wenn nicht vorhanden, versuche, ihn aus der Session zu holen.
        // Wenn auch das nicht funktioniert, setze den Standardwert auf 'up'.
        $sort = $request->get('sort')??$request->getSession()->get('software_sort')??'up';
        $sort_field = $request->get('sort_field')??$request->getSession()->get('software_sort_field')??'id';

        // Fetch all Software from the database
        $repository = $this->headerService->entityManager->getRepository(Software::class);
        $queryBuilder = $repository->filernAndSortBy($search,$sort_field,$sort);

        // Paginate the results of the query
        $pagination = $paginator->paginate(
            $queryBuilder, // QueryBuilder
            $page,         // Aktuelle Seite
            $limitPPage    // Limit pro Seite
        );

        // Abrufen des aktuell authentifizierten Benutzers
        $userToken      = $this->getUser();
        $userRole       = $userToken->getRoles();

        // suchen nach aktuelle Role --> um die aktuelle Recht zu finden
        // default Role ist die, die höher Recht hat (kleiner role ID)
        // Holen Sie sich die Schlüssel des Arrays
        $keys           = array_keys($userRole);
        // Finden Sie den kleinsten Schlüssel
        $smallestKey    = min($keys);
        $defaultRole    = $userRole[$smallestKey];
        $currentRoleId  = intval($request->getSession()->get('currentRoleId')); // phpstan psalm  ---> für mehr type,.. function check #####

        $currentPage    = $pagination->getCurrentPageNumber();
        $totalCount     = $pagination->getTotalItemCount();
        $beginItem      = $currentPage * $limitPPage - $limitPPage + 1;
        $endItem        = ($currentPage * $limitPPage > $totalCount) ? $totalCount : $currentPage * $limitPPage;

        //GET-Daten in der Session speichern
        $request->getSession()->set('currentParams', $request->attributes->get('_route_params'));
        // POST-Daten in der Session speichern
        $request->getSession()->set('software_sort', $sort);
        $request->getSession()->set('software_sort_field', $sort_field);

        // define an array for icon
        $iconSources = [
            'source_symbol_img'         => "/{$this->headerService::ICONS_PATH['logo']}",
            'source_user_img'           => '/'.$this->headerService::ICONS_PATH['user'],
            'source_forward_img'        => '/'.$this->headerService::ICONS_PATH['forward'],
            'source_backward_img'       => '/'.$this->headerService::ICONS_PATH['backward'],
            'source_forward_end_img'    => '/'.$this->headerService::ICONS_PATH['forward_end'],
            'source_backward_end_img'   => '/'.$this->headerService::ICONS_PATH['backward_end'],
            'source_delete_img'         => '/'.$this->headerService::ICONS_PATH['delete'],
            'source_up_img'             => '/'.$this->headerService::ICONS_PATH['up'],
            'source_down_img'           => '/'.$this->headerService::ICONS_PATH['down'],
        ];

        // Group user-related data
        $userData = [
            'user'                      => $userToken,
            'userRole'                  => $userRole,
            'defaultRole'               => $defaultRole,
            'currentRoleId'             => $currentRoleId,
        ];

        // Group pagination-related data
        $paginationData = [
            'pathName'                  => $request->attributes->get('_route'),
            'currentPage'               => $currentPage,
            'limitPPage'                => $limitPPage,
            'search'                    => $search,
            'totalCount'                => $totalCount,
            'softwares'                 => $pagination,
            'beginItem'                 => $beginItem,
            'endItem'                   => $endItem,
            'sort'                      => $sort,
            'properties'                => $properties,
            'sort_field'                => $sort_field,
        ];

        // Combine all data into a single array
        $data = array_merge($userData, $paginationData, $iconSources,[
            'task'              => 'software_list',
        ]);
        return $this->render('Software/software_list.html.twig', $data);
    }


    #[Route('/software/edit/{id}',
        name: 'software_edit',
        requirements: ['id'=>'\d+']
    )]
    public function edit(Software $software, Request $request): Response
    {
        $form =  $this -> createForm(SoftwareType::Class, $software, ['edit' => true]); // lesen die Daten von Datenbank
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->headerService->entityManager->flush(); //

            return $this->redirectToRoute('software_list',$request->getSession()->get('currentParams'));
        }

        $userToken      = $this->getUser();
        $userRole       = $userToken->getRoles();

        // suchen nach aktuelle Role --> um die aktuelle Recht zu finden
        // default Role ist die, die höher Recht hat (kleiner role ID)
        // Holen Sie sich die Schlüssel des Arrays
        $keys           = array_keys($userRole);
        // Finden Sie den kleinsten Schlüssel
        $smallestKey    = min($keys);
        $defaultRole    = $userRole[$smallestKey];

        return $this->render('Software/software_edit.html.twig', [
            'task'                      => 'software_edit',
            'user'                      => $userToken,
            'userRole'                  => $userRole,
            'defaultRole'               => $defaultRole,
            'source_symbol_img'         => '/'.$this->headerService::ICONS_PATH['logo'],
            'source_user_img'           => '/'.$this->headerService::ICONS_PATH['user'],
            'form'                      => $form
        ]);
    }


    #[Route('/software/new', name: 'software_new')]
    public function new(Request $request):Response
    {
        $software = new Software();
        $form = $this->createForm(SoftwareType::class, $software);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Speichern Sie die Software-Entität in der Datenbank
            $this->headerService->entityManager->persist($software);
            $this->headerService->entityManager->flush();

            return $this->redirectToRoute('software_list'); // Beispielroute nach dem Speichern
        }

        $userToken = $this->getUser();
        $userRole       = $userToken->getRoles();

        // suchen nach aktuelle Role --> um die aktuelle Recht zu finden
        // default Role ist die, die höher Recht hat (kleiner role ID)
        // Holen Sie sich die Schlüssel des Arrays
        $keys           = array_keys($userRole);
        // Finden Sie den kleinsten Schlüssel
        $smallestKey    = min($keys);
        $defaultRole    = $userRole[$smallestKey];

        return $this->render('Software/software_new.html.twig',[
            'task'                      => 'software_new',
            'user'                      => $userToken,
            'userRole'                  => $userRole,
            'defaultRole'               => $defaultRole,
            'source_symbol_img'         => '/'.$this->headerService::ICONS_PATH['logo'],
            'source_user_img'           => '/'.$this->headerService::ICONS_PATH['user'],
            'form'=>$form
        ]);
    }

    // ?? kann man die gleiche URL von software list benutzen ?
    #[Route('/software/ajax/delete/{id}', name: 'software_delete')]  // was passiert, wenn benutzer diese URL kennt --> wie kann man das verhindern???
    public function delete(Software $software): JsonResponse
    {
        $this->headerService->entityManager->remove($software);
        $this->headerService->entityManager->flush();

        return new JsonResponse('Löschen erfolg !');
    }

    #[Route('/software/ajax/getCurrentRole', name: 'current_role')]
    public function process(Request $request): JsonResponse
    {
        // Get the value sent from jQuery
        $value = $request->request->get('value');

        // push the changed value to session
        $request->getSession()->set('currentRoleId', $value);

        // Process the value (e.g., save to database, perform calculations, etc.)
        // For demonstration, let's just return the value
        return new JsonResponse(['received' => $value]);
    }
}
