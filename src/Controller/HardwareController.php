<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\File;
use App\Entity\Hardware;
use App\Repository\HardwareRepository;
use App\Service\HardwareService;
use App\Form\HardwareType;
use App\Service\HeaderService;
use App\Service\FileService;
use App\Service\UserService;
use App\Service\WeatherService;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;


class HardwareController extends AbstractController
{
    const DAFAULT_LENGTH = 5;
    const ENTITY_NAME = 'hardware';

    public function __construct(
        private readonly HeaderService      $headerService,
        private readonly HardwareService    $hardwareService,
        private readonly FileService        $fileService,
        private readonly UserService        $userService,
        private readonly HardwareRepository $hardwareRepository,
    ){}


    // Function add a new hardware in DB ------------------------------------------------------------------------
    #[Route('/hardware/local/new', name: 'hardware_local_new')]
    public function newLocalHardware(Request $request):Response
    {
        $hardware = new Hardware();
        $form = $this->createForm(HardwareType::class, $hardware);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Speichern Sie die Software-Entität in der Datenbank
            $this->headerService->entityManager->persist($hardware);
            $this->headerService->entityManager->flush();
            return $this->redirectToRoute('hardware_local_list');
        }

        // Abrufen des aktuell authentifizierten Benutzers
        $user           = $this->getUser();
        $userInfor      = $this->userService->inforUser($user);
        $currentRoleId  = intval($request->getSession()->get('currentRoleId'));

        // Combine all data into a single array
        $data = array_merge($userInfor, $this->headerService::ICONS_PATH_HARDWARE, [
            'task'           => 'hardware_local_edit',
            'pathName'       => $request->attributes->get('_route'),
            'currentRoleId'  => $currentRoleId,
            'form'           =>$form
        ]);
        return $this->render('Hardware/Local/hardware_local_new.html.twig', $data);
    }

    // Function Show list of all hardware in DB -----------------------------------------------------------------
    #[Route('/hardware/local/list/{limitPPage}/{page}/{search}',
        name: 'hardware_local_list',
        requirements: [ // wenn man hier req definiert --> ERROR Meldung  ???
            'limitPPage'    => '\d+',
            'page'          => '\d+',
            'search'        => '.+',
        ]
    )]
    public function listLocalHardware(
        Request $request,
        PaginatorInterface $paginator,
        int $limitPPage = self::DAFAULT_LENGTH,
        int $page       = 1,
        string $search  = '',
    ): Response
    {
        $properties = (new ReflectionClass(Hardware::class))->getProperties(); // Anzahl der Eigenschaften zählen
        $sort = $request->get('sort')??$request->getSession()->get('hardware_sort_direction')??'up';
        $sortField = $request->get('sort_field')??$request->getSession()->get('hardware_sort_field')??'id';

//        dd($sort, $sortField); ##############################################################

        // Fetch all Software from the database
        $queryBuilder   = $this->hardwareRepository->filterAndSort($search, 'name', $sort, $sortField);
        $sortPagination = $this->headerService->sort_pagination(
            $queryBuilder,
            $limitPPage,
            $page,
            $search,
            $paginator);

        // Abrufen des aktuell authentifizierten Benutzers
        $user           = $this->getUser();
        $userInfor      = $this->userService->inforUser($user);
        $currentRoleId  = intval($request->getSession()->get('currentRoleId'));

        //GET-Daten in der Session speichern
        $request->getSession()->set('currentParams', $request->attributes->get('_route_params'));
        // POST-Daten in der Session speichern
        $request->getSession()->set('hardware_sort_direction', $sort);
        $request->getSession()->set('hardware_sort_field', $sortField);

        // Combine all data into a single array
        $data = array_merge($userInfor, $sortPagination, $this->headerService::ICONS_PATH_HARDWARE, [
            'task'                      => 'hardware_local_list',
            'pathName'                  => $request->attributes->get('_route'),
            'properties'                => $properties,
            'sort_field'                => $sortField,
            'currentRoleId'             => $currentRoleId,
        ]);
        return $this->render('Hardware/Local/hardware_local_list.html.twig', $data);
    }

    // Function edit hardware in Hardware API -------------------------------------------------------------------
    /**
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws Exception
     */
    #[Route('/hardware/local/edit/{id}', name: 'hardware_local_edit')]
    public function editHardwareLocal(Hardware $hardware, Request $request, SluggerInterface $slugger):Response
    {
        // read files, the already save in table file in database. create FilenameArray
        $fileExists = $hardware->getFile()->toArray();
        $fileExistsArray = array_map(function ($file) {
           return [
               'fileName' => basename(base64_decode($file->getEncryptedPath())),
           ];
        }, $fileExists);
        $fileNames = array_column($fileExistsArray, 'fileName');

        // read image Path from database
        $imagePath = $hardware->getImage()?->getDateipfad(); // ---> für den fall image in public Ordner
        $image = $hardware->getImage();

        $form =  $this -> createForm(HardwareType::Class, $hardware, ['edit' => true, 'image' => isset($imagePath)]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('file')->getData();
            $image = ($imagePath)? null: $form->get('image')->getData();

            if($file){
                $this->fileService->uploadFileHardware($file, $hardware);
            }
            if($image){
                $uploadImage = $this->fileService->uploadImgHardware($image, $hardware);
                $hardware->setImage($uploadImage);
            }

            // save Hardware Update in Database
            $this->headerService->entityManager->persist($hardware);
            $this->headerService->entityManager->flush();

            return $this->redirectToRoute('hardware_local_list');
        }

        // Abrufen des aktuell authentifizierten Benutzers
        $user           = $this->getUser();
        $userInfor      = $this->userService->inforUser($user);
        $currentRoleId  = intval($request->getSession()->get('currentRoleId'));

        // Combine all data into a single array
        $data = array_merge($userInfor, $this->headerService::ICONS_PATH_HARDWARE, [
            'task'                      => 'hardware_local_edit',
            'pathName'                  => $request->attributes->get('_route'),
            'currentRoleId'             => $currentRoleId,
            'fileNames'                 => $fileNames,
            'imagePath'                 => $imagePath,
            'image'                     => $image,
            'form'                      => $form
        ]);
//        dd($data);
        return $this->render('Hardware/Local/hardware_local_edit.html.twig', $data);
    }


    #[Route('/ajax/hardware/local/delete/{id}', name: 'hardware_delete')]
    public function delete(Hardware $software): JsonResponse
    {
        $this->headerService->entityManager->remove($software);
        $this->headerService->entityManager->flush();

        return new JsonResponse('Löschen erfolg !');
    }

    #[Route('/ajax/hardware/getCurrentRole', name: 'hardware_current_role')]
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









    // Function add a new hardware in Hardware API --------------------------------------------------------------
    #[Route('/hardware/api/new', name: 'hardware_api_new')]
    public function createHardwareAPI(Request $request):Response
    {
        $hardwareApi = new Hardware();
        $form = $this->createForm(HardwareType::class, $hardwareApi, ['api'=>true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hardwareData = [
                'name'          => $hardwareApi->getName(),
                'bezeichnen'    => $hardwareApi->getBezeichnen(),
                'beschreibung'  => $hardwareApi->getBeschreibung(),
                'kommentar'     => $hardwareApi->getkommentar(),
            ];
            try{
                $this->hardwareService->createHardware($hardwareData);
            }catch(Exception $e){
                return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
            }
            return $this->redirectToRoute('hardware_api_list');
        }

        // Abrufen des aktuell authentifizierten Benutzers
        $user           = $this->getUser();
        $userInfor      = $this->userService->inforUser($user);
        $currentRoleId  = intval($request->getSession()->get('currentRoleId'));

        // Combine all data into a single array
        $data = array_merge($userInfor, $this->headerService::ICONS_PATH_HARDWARE, [
            'pathName'                  => $request->attributes->get('_route'),
            'currentRoleId'             => $currentRoleId,
            'form'                      => $form
        ]);
        return $this->render('Hardware/API/hardware_api_new.html.twig', $data);
    }

    // Function Show list of all hardware in Hardware API -------------------------------------------------------
    #[Route('/hardware/api/list', name: 'hardware_api_list')]
    public function listHardwareAPI(Request $request): Response
    {
        // Abrufen des aktuell authentifizierten Benutzers
        $user           = $this->getUser();
        $userInfor      = $this->userService->inforUser($user);
        $currentRoleId  = intval($request->getSession()->get('currentRoleId'));

        try {
            $hardwareDatas = $this->hardwareService->getHardwareListData();

            // Combine all data into a single array
            $data = array_merge($userInfor, $this->headerService::ICONS_PATH_HARDWARE, [
                'pathName'                  => $request->attributes->get('_route'),
                'currentRoleId'             => $currentRoleId,
                'hardwareDatas'             => $hardwareDatas
            ]);

            return $this->render('Hardware/API/hardware_api_list.html.twig', $data);

        } catch (Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    // Function edit hardware in Hardware API -------------------------------------------------------------------
    #[Route('/hardware/api/edit/{id}', name: 'hardware_api_edit')]
    public function editHardwareAPI($id, SerializerInterface $serializer, Request $request):Response
    {
        $hardwareData = new Hardware();
        try{
            $hardwareApi = $this->hardwareService->getHardwareData($id);

            $hardwareData->setId($hardwareApi['id']);
            $hardwareData->setName($hardwareApi['name']);
            $hardwareData->setBezeichnen($hardwareApi['bezeichnen']);
            $hardwareData->setBeschreibung($hardwareApi['beschreibung']);
            $hardwareData->setKommentar($hardwareApi['kommentar']);

            $form = $this->createForm(HardwareType::class, $hardwareData, ['edit' => true, 'api'=>true]);
            $form->handleRequest($request);

            $aenderung = $serializer->normalize($form->getData());

            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $this->hardwareService->updateHardware($hardwareApi['id'], $aenderung);
                    return $this->redirectToRoute('hardware_api_list');
                }catch (Exception $e){
                    return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
                }
            }
        }catch(Exception $e){
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        // Abrufen des aktuell authentifizierten Benutzers
        $user           = $this->getUser();
        $userInfor      = $this->userService->inforUser($user);
        $currentRoleId  = intval($request->getSession()->get('currentRoleId'));

        // Combine all data into a single array
        $data = array_merge($userInfor, $this->headerService::ICONS_PATH_HARDWARE, [
            'pathName'                  => $request->attributes->get('_route'),
            'currentRoleId'             => $currentRoleId,
            'form'                      => $form
        ]);
        return $this->render('Hardware/API/hardware_api_edit.html.twig', $data);
    }

    // Function Show one hardware in Hardware API ---------------------------------------------------------------
    #[Route('/hardware/api/show/{id}', name: 'hardware_api_show')]
    public function showHardwareAPI($id): JsonResponse
    {
        try {
            $hardwareData = $this->hardwareService->getHardwareData($id);
            return $this->json($hardwareData);
        } catch (Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }









//    #[Route('/weather/{city}', name: 'weather_show')]
//    public function weather_show($city): JsonResponse
//    {
//        try {
//            $weatherData = $this->weatherService->getWeatherData($city);
//            return $this->json($weatherData);
//        } catch (\Exception $e) {
//            return $this->json(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
//        }
//    }

}
