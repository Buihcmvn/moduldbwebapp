<?php
declare(strict_types=1);

// src/Controller/ApiController.php
namespace App\Controller;

use App\Entity\Area;
use App\Entity\Hardware;
use App\Entity\Projekte;
use App\Entity\Software;
use App\Repository\AreaRepository;
use App\Repository\HardwareRepository;
use App\Repository\MeilensteinRepository;
use App\Repository\ProjekteRepository;
use App\Repository\SoftwareRepository;
use App\Service\HeaderService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ApiController extends AbstractController
{
    const DAFAULT_LENGTH = 5;

    public function __construct(
        private readonly MeilensteinRepository      $meilensteinRepository,
        private readonly HardwareRepository         $hardwareRepository,
        private readonly ProjekteRepository         $projekteRepository,
        private readonly HeaderService              $headerService,
        private readonly EntityManagerInterface     $entityManager,
        private readonly CsrfTokenManagerInterface  $csrfTokenManager,
        private readonly ValidatorInterface         $validator
    ) {}


    // User Data #####################################################################################################
    #[Route('/api/user-data', name: 'api_user_data', methods: ['GET'])]
    public function getUserData(): JsonResponse
    {
        $userToken      = $this->getUser();
        $userRole       = $userToken->getRoles();

        $data = [
            'user' => $userToken,
            'userRole' => $userRole,
        ];

        return $this->json($data);
    }


    // Hardware Data #################################################################################################
    #[Route('/api/hardware/list', name: 'api_hardware_list')]
    public function showListHardware(HardwareRepository $hardwareRepository):JsonResponse
    {
        $hardwareList = $hardwareRepository->findAll();

        $data = [];
        foreach ($hardwareList as $hardware) {
            $data[] = [
                'id' => $hardware->getId(),
                'name' => $hardware->getName(),
            ];
        }
        return new JsonResponse($data);
    }


    // get data from hardware with id
    /**
     * @throws \ReflectionException
     */
    #[Route('/api/hardware/get/{id}',name: 'api_hardware_get', methods: ['GET'])]
    public function getHardware(Hardware $hardware): JsonResponse
    {

        $data[] = $hardware->toArray();
        return new JsonResponse($data);
    }


    /**
     * @throws \ReflectionException
     */
    #[Route('/api/hardware/local/list/{limitPPage}/{page}/{sort}/{sortField}/{search}',
        name: 'api_hardware_list',
        requirements: [
            'limitPPage'    => '\d+',
            'page'          => '\d+',
            'search'        => '.+',
        ]
    )]
    public function getHardwareList(
        PaginatorInterface $paginator,
        int $limitPPage = self::DAFAULT_LENGTH,
        int $page       = 1,
        string $sort = 'up',
        string $sortField = 'id',
        string $search  = '',
    ): JsonResponse
    {
        $queryBuilder = $this->hardwareRepository->filterAndSort($search, 'name', $sort, $sortField);

        $sortPagination = $this->headerService->sort_pagination_json(
            $queryBuilder,
            $limitPPage,
            $page,
            $search,
            $paginator
        );

        return new JsonResponse($sortPagination);
    }

    // Meilenstein Data ##############################################################################################

    #[Route('/api/meilenstein/get', name: 'api_meilenstein_get')]
    public function showListMeilenstein(MeilensteinRepository $meilensteinRepository):JsonResponse
    {
        $meilensteinList = $meilensteinRepository->findAll();

        $data = [];
        foreach ($meilensteinList as $meilenstein) {
            $data[] = $meilenstein->toArray();
        }
        return new JsonResponse($data);
    }


    /**
     * @throws \ReflectionException
     */
    #[Route('/api/meilenstein/list/{limitPPage}/{page}/{search}',
        name: 'api_meilenstein_list',
        requirements: [
            'limitPPage'    => '\d+',
            'page'          => '\d+',
            'search'        => '.+',
        ]
    )]
    public function getMeilensteinList(
        PaginatorInterface  $paginator,
        int                 $limitPPage = self::DAFAULT_LENGTH,
        int                 $page       = 1,
        string              $sort       = 'up',
        string              $sortField  = 'id',
        string              $search     = '',
    ): JsonResponse
    {
        $queryBuilder = $this->meilensteinRepository->filterAndSort($search,'name', $sort, $sortField);
        $sortPagination = $this->headerService->sort_pagination_json(
            $queryBuilder,
            $limitPPage,
            $page,
            $search,
            $paginator
        );
        return new JsonResponse($sortPagination);
    }




    // Software Data #################################################################################################
    #[Route('/api/software/get', name: 'api_software_get')]
    public function showListSoftware(SoftwareRepository $softwareRepository):JsonResponse
    {
        $softwareList = $softwareRepository->findAll();

        $data = [];
        foreach ($softwareList as $software) {
            $data[] = [
                'id' => $software->getId(),
                'name' => $software->getName(),
            ];
        }
        return new JsonResponse($data);
    }


    // Projekte Data #################################################################################################
    /**
     * @throws \ReflectionException
     * hold only project properties
     */
    #[Route('/api/projekt/get/{id}',
        name: 'api_projekt_get',
        requirements: [
            'id'    => '\d+',
        ]
    )]
    public function getProject(Projekte $projekte): JsonResponse
    {
        $objArray = $this->headerService->loadPersistentCollection($projekte);
        return new JsonResponse($objArray);
    }


    /**
     * @throws \ReflectionException
     */
    #[Route('/api/projekte/list/{limitPPage}/{page}/{sort}/{sortField}/{search}',
        name: 'api_projekte_list',
        requirements: [ // wenn man hier req definiert --> ERROR Meldung  ???
            'limitPPage'    => '\d+',
            'page'          => '\d+',
            'search'        => '.+',
        ]
    )]
    public function getProjekte(
        PaginatorInterface $paginator,
        int $limitPPage = self::DAFAULT_LENGTH,
        int $page       = 1,
        string $sort = 'up',
        string $sortField = 'id',
        string $search  = '',
    ): JsonResponse
    {
        $queryBuilder = $this->projekteRepository->filterAndSort($search, 'name', $sort, $sortField);
        $sortPagination = $this->headerService->sort_pagination_json(
            $queryBuilder,
            $limitPPage,
            $page,
            $search,
            $paginator
        );
        return new JsonResponse($sortPagination);
    }


    /**
     * @throws \ReflectionException
     * hold project properties but hold all area, hardware, software
     */
    #[Route('/api/projekt/gets/{id}', name: 'api_projekt_edit_data')]
    public function getEditData(
        Projekte $projekte,
        AreaRepository $areaRepository,
        HardwareRepository $hardwareRepository,
        SoftwareRepository $softwareRepository
    ): JsonResponse {
        // Lấy dữ liệu từ database
        $areas = $areaRepository->findAll();
        $hardwareList = $hardwareRepository->findAll();
        $softwareList = $softwareRepository->findAll();
        $projektData = $this->headerService->loadPersistentCollection($projekte);

        $savedAreas = array_column($projektData['area'],'id');
        $savedHardwares = array_column($projektData['hardware'],'id');
        $savedSoftwares = array_column($projektData['software'],'id');

//        dd($savedAreas, $savedHardwares, $savedSoftwares);

        $data = [
            'id' => $projekte->getId(),
            'name' => $projekte->getName(),
            'beschreibung' => $projekte->getBeschreibung(),
            'kommentar' => $projekte->getKommentar(),
            'areas' => array_map(function ($area) use ($savedAreas) {
                return [
                    'id' => $area->getId(),
                    'name' => $area->getName(),
                    'selected' => (in_array($area->getId(), $savedAreas))?? false,
                ];
            }, $areas),
            'hardwareList' => array_map(function ($hardware) use ($savedHardwares) {
                return [
                    'id' => $hardware->getId(),
                    'name' => $hardware->getName(),
                    'selected' => (in_array($hardware->getId(), $savedHardwares))?? false,
                ];
            }, $hardwareList),
            'softwareList' => array_map(function ($software) use ($savedSoftwares) {
                return [
                    'id' => $software->getId(),
                    'name' => $software->getName(),
                    'selected' => (in_array($software->getId(), $savedSoftwares))?? false,
                ];
            }, $softwareList),
        ];

//        dd($savedAreas, $savedHardwares, $savedSoftwares, $data);

        return $this->json($data);
    }


    #[Route('/api/projekt/edit/{id}', name: 'api_projekt_edit', methods: ['PUT'])]
    public function edit(Request $request, Projekte $projekte): JsonResponse
    {
        // 1. CSRF Validation
        // (compare between token created from server (data-csrf-token="{{ csrf_token('edit_projekte') }}") with token from Request)
        $csrfToken = new CsrfToken('edit_projekte', $request->headers->get('X-CSRF-Token'));
        if (!$this->csrfTokenManager->isTokenValid($csrfToken)) {
            return new JsonResponse(['message' => 'Invalid CSRF token'], 400);
        }

        // 2. Decode JSON Data
        $data = json_decode($request->getContent(), true);

        if ($data === null) {
            return new JsonResponse(['message' => 'Invalid JSON'], 400);
        }

        // 3. Data Validation
        if (empty($data['name'])) {
            return new JsonResponse(['message' => 'Name cannot be empty'], 400);
        }

        // 4. Update Entity
        $projekte->setName($data['name']);
        $projekte->setBeschreibung($data['beschreibung'] ?? null); // Use null coalesce operator
        $projekte->setKommentar($data['kommentar'] ?? null);       // Use null coalesce operator

        // 5. Handle Areas, Hardware, Software (Assuming you are sending IDs)
        $this->updateManyToManyRelation($projekte, $data['areas'], 'area', Area::class);
        $this->updateManyToManyRelation($projekte, $data['hardwareList'], 'hardware', Hardware::class);
        $this->updateManyToManyRelation($projekte, $data['softwareList'], 'software', Software::class);

        // 6. Validation using Symfony Validator
        $errors = $this->validator->validate($projekte);

        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            return new JsonResponse($errorMessages, 400);
        }

        // 7. Save to Database
        $this->entityManager->persist($projekte);
        $this->entityManager->flush();

        // 8. Return Success Response
        return new JsonResponse(['message' => 'Project updated successfully'], 200);
    }

    private function updateManyToManyRelation(Projekte $projekte, array $newIds, string $relationName, string $entityClass): void
    {
        $currentCollection = $projekte->{'get' . ucfirst($relationName)}(); // e.g., $projekte->getArea()
        $currentIds = $currentCollection->map(function ($entity) {
            return $entity->getId();
        })->toArray();

        $idsToRemove = array_diff($currentIds, $newIds);
        $idsToAdd = array_diff($newIds, $currentIds);

        $entityRepository = $this->entityManager->getRepository($entityClass);

        foreach ($idsToRemove as $id) {
            $entity = $entityRepository->find($id);
            if ($entity) {
                $projekte->{'remove' . ucfirst($relationName)}($entity); // e.g., $projekte->removeArea($entity)
            }
        }

        foreach ($idsToAdd as $id) {
            $entity = $entityRepository->find($id);
            if ($entity) {
                $projekte->{'add' . ucfirst($relationName)}($entity); // e.g., $projekte->addArea($entity)
            }
        }
    }



    #[Route('/api/projekt/delete/{id}', name: 'api_projekt_delete', methods: ['DELETE'])]
    public function delete(Request $request, Projekte $projekte): JsonResponse
    {
        // 1. CSRF Validation
        // (compare between token created from server (data-csrf-token="{{ csrf_token('edit_projekte') }}") with token from Request)
        $csrfToken = new CsrfToken('list_projekte', $request->headers->get('X-CSRF-Token'));
        if (!$this->csrfTokenManager->isTokenValid($csrfToken)) {
            return new JsonResponse(['message' => 'Invalid CSRF token'], 400);
        }

        // 2. Delete to Database
        $this->headerService->entityManager->remove($projekte);
        $this->headerService->entityManager->flush();

        // 3. Return Success Response
        return new JsonResponse(['message' => 'Löschen erfolg !'], 200);
    }
}
