<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Software;
use Doctrine\Persistence\ManagerRegistry;


class SoftwareRepository extends BaseRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Software::class);
    }

    public function filernAndSortBy($search, $column ='id',$sort='down'): \Doctrine\ORM\QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('s');
            // Filter anwenden, wenn ein Suchbegriff vorhanden ist
        if (isset($search)) {
            $queryBuilder->andWhere('s.name LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        // Determine sort direction
        $sortDirection = ($sort === 'down') ? 'desc' : 'asc';

        // Sortierung hinzufügen
        $queryBuilder->orderBy('s.'.$column, $sortDirection);
        return $queryBuilder;
    }


    public function filterByName($name): ?array
    {
        return $this->createQueryBuilder('u')
            ->where('u.name LIKE :search')
            ->setParameter('search', '%' . $name . '%')
            ->getQuery()
            ->getResult();
    }

    public function filterByNameMaxShow($search, $maxResult): ?array
    {
        return $this->createQueryBuilder('u')
            ->where('u.name LIKE :search')
            ->setParameter('search', '%' . $search . '%')
            ->setMaxResults($maxResult)
            ->getQuery()
            ->getResult();
    }

    public function selectMaxShow($maxResult): ?array
    {
        return $this->createQueryBuilder('u')
            ->setMaxResults($maxResult)
            ->getQuery()
            ->getResult();
    }


    //    /**
    //     * @return Software[] Returns an array of Software objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Software
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
