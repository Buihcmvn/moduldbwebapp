<?php
declare(strict_types=1);
/*
 *  Dies Service wird für allgemeinte Information mitgeliefert
 *  - gelogte User ID, User Role
 *  - Horizontal Bar Infor
 */

namespace App\Service;

use Doctrine\ORM\PersistentCollection;
use ReflectionClass;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;


class HeaderService
{
    const ICONS_PATH = [
        'home'          =>'icons/home.svg',
        'user'          =>'icons/user.svg',
        'forward'       =>'icons/forward.svg',
        'backward'      =>'icons/backward.svg',
        'backward_end'  =>'icons/backward_end.svg',
        'forward_end'   =>'icons/forward_end.svg',
        'delete'        =>'icons/delete.svg',
        'settings'      =>'icons/settings.svg',
        'up'            =>'icons/up.svg',
        'down'          =>'icons/down.svg',
    ];

    const ICONS_PATH_HARDWARE = [
        'home'          =>'icons/home.svg',
        'user'          =>'icons/user.svg',
        'forward'       =>'icons/forward.svg',
        'backward'      =>'icons/backward.svg',
        'backward_end'  =>'icons/backward_end.svg',
        'forward_end'   =>'icons/forward_end.svg',
        'delete'        =>'icons/delete.svg',
        'settings'      =>'icons/settings.svg',
        'up'            =>'icons/up.svg',
        'down'          =>'icons/down.svg',
    ];

    public EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    // für den Fall speichern und management Image Name in eine Tabelle
    public function sort_pagination( $queryBuilder,int $limitPPage, int $page, string $search, PaginatorInterface $paginator): array
    {

        // Paginate the results of the query
        $pagination = $paginator->paginate(
            $queryBuilder, // QueryBuilder
            $page,         // Aktuelle Seite
            $limitPPage    // Limit pro Seite
        );

        $currentPage    = $pagination->getCurrentPageNumber();
        $totalCount     = $pagination->getTotalItemCount();
        $beginItem      = $currentPage * $limitPPage - $limitPPage + 1;
        $endItem        = ($currentPage * $limitPPage > $totalCount) ? $totalCount : $currentPage * $limitPPage;

        return [
            'list'                      => $pagination,
            'currentPage'               => $currentPage,
            'limitPPage'                => $limitPPage,
            'search'                    => $search,
            'totalCount'                => $totalCount,
            'beginItem'                 => $beginItem,
            'endItem'                   => $endItem,
        ];
    }

    /**
     * @throws \ReflectionException
     */
    public function sort_pagination_json($queryBuilder, int $limitPPage, int $page, string $search, PaginatorInterface $paginator): array
    {
        $list = [];
        // Paginate the results of the query
        $pagination = $paginator->paginate(
            $queryBuilder, // QueryBuilder
            $page,         // Aktuelle Seite
            $limitPPage    // Limit pro Seite
        );

        $currentPage    = $pagination->getCurrentPageNumber();
        $totalCount     = $pagination->getTotalItemCount();
        $beginItem      = $currentPage * $limitPPage - $limitPPage + 1;
        $endItem        = ($currentPage * $limitPPage > $totalCount) ? $totalCount : $currentPage * $limitPPage;

        # wie kann man keys automaitk erkennen ?
        foreach ($pagination->getItems() as $item) {
            $itemArray = $this->loadPersistentCollection($item,"name");
            $list[] = $itemArray;
        }
        return [
            'list'                      => $list,
            'currentPage'               => $currentPage,
            'limitPPage'                => $limitPPage,
            'search'                    => $search,
            'totalCount'                => $totalCount,
            'beginItem'                 => $beginItem,
            'endItem'                   => $endItem,
        ];
    }

    /**
     * @throws \ReflectionException
     */
    public function loadPersistentCollection($Object, $name=null):array
    {
        $objArray = $Object->toArray();

        // use Reflection to check all properties of object
        $reflection = new ReflectionClass($Object);
        $properties = $reflection->getProperties();

        foreach ($properties as $property) {
            $propertyName = $property->getName();

            // check properties is PersistentCollection
            if (isset($objArray[$propertyName]) && $objArray[$propertyName] instanceof PersistentCollection) {
                $collectionArrays = [];
                foreach ($objArray[$propertyName] as $collectionItem) {
                    if (method_exists($collectionItem, 'toArray')) {
                        $collectionArrays[] = $collectionItem->toArray();
                    } else {
                        // if collectionItem doesnt have toArray(), take default infor
                        $collectionArrays[] = [
                            'id' => $collectionItem->getId(),
                            'name' => (string) $collectionItem, // force to string (need to have __toString())
                        ];
                    }
                }
                $objArray[$propertyName] = ($name)? implode(",", array_column($collectionArrays, 'name')): $collectionArrays;
            }
        }
        return $objArray;
    }
}