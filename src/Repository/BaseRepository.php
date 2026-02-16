<?php
declare(strict_types=1);

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

// Loại bỏ #[AllowDynamicProperties] nếu không cần thiết

abstract class BaseRepository extends ServiceEntityRepository
{
    protected \Doctrine\ORM\EntityManagerInterface $entityManager;
    protected string $entityClass;
    protected string $alias = 'entity'; // Alias mặc định

    public function __construct(ManagerRegistry $registry, string $entityClass)
    {
        parent::__construct($registry, $entityClass);

        $this->entityManager = $this->getEntityManager();
        $this->entityClass = $entityClass;

        // check if $entityClass is a Entity
        if (!$this->isEntityClass($entityClass)) {
            throw new \InvalidArgumentException("Entity class {$entityClass} does not exist");
        }
    }

    private function isEntityClass(string $entityClass): bool
    {
        try {
            $metadata = $this->entityManager->getClassMetadata($entityClass);
            return $metadata instanceof ClassMetadata;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Filters and sorts entities based on the given criteria.
     *
     * @param string|null $search The search term (optional).
     * @param string $searchField The field to search in (default: 'name').
     * @param string $sortDirection The sort direction ('asc' or 'desc', default: 'down' which is converted to 'desc').
     * @param string $sortField The field to sort by (default: 'id').
     * @param string|null $alias The alias to use in the query (optional, defaults to the class property).
     *
     * @return QueryBuilder
     * !Achtung für den Fall:
     *  $sortField ist nicht eine String property sondern ein property von association des aktuelle Entity
     */
    public function filterAndSort(?string $search, string $searchField = 'name', string $sortDirection = 'down', string $sortField = 'id', ?string $alias = null): QueryBuilder
    {
        $alias = $alias ?? $this->alias;
        $queryBuilder = $this->createQueryBuilder($alias);
        $classMetadata = $this->entityManager->getClassMetadata($this->entityClass);

        if ($search) {
            $queryBuilder->andWhere($queryBuilder->expr()->like($alias . '.' . $searchField, ':search'))
                ->setParameter('search', '%' . $search . '%');
        }

        $sortDirection = (strtolower($sortDirection) === 'down') ? 'DESC' : 'ASC';
        $sortFieldForQuery = $alias . '.' . $sortField;

        // filter sortField as a property of an Entity
        if (str_contains($sortField, '_')) {
            [$sortRelatedEntity, $sortRelatedProperty] = explode('_', $sortField, 2);

            if ($classMetadata->hasAssociation($sortRelatedEntity)) {
                $sortRelatedAlias = 's_' . $sortRelatedEntity;

                $associationMapping = $classMetadata->getAssociationMapping($sortRelatedEntity);

                if ($associationMapping['type'] & ClassMetadata::MANY_TO_MANY) {
                    // ManyToMany requires a subquery for sorting
                    $subQueryBuilder = $this->entityManager->createQueryBuilder();
                    $subQueryBuilder->select('MIN(' . $sortRelatedAlias . '.' . $sortRelatedProperty . ')') // or MAX, depending on your needs
                    ->from($associationMapping['targetEntity'], $sortRelatedAlias)
                        ->join($sortRelatedAlias . '.' . $associationMapping['inversedBy'], 's_' . $alias)  // Or inversedBy
                        ->where('s_' . $alias . '.id = ' . $alias . '.id'); // Correlate with the main query

                    $queryBuilder->addSelect('(' . $subQueryBuilder->getDQL() . ') AS HIDDEN sort_field');
                    $sortFieldForQuery = 'sort_field';
                } else {
                    // OneToMany or ManyToOne
                    $queryBuilder->leftJoin($alias . '.' . $sortRelatedEntity, $sortRelatedAlias);
                    $sortFieldForQuery = $sortRelatedAlias . '.' . $sortRelatedProperty;
                }
            } else {
                throw new \InvalidArgumentException(sprintf('Invalid association "%s" in sort field "%s".', $sortRelatedEntity, $sortField));
            }
        }
        $queryBuilder->orderBy($sortFieldForQuery, $sortDirection);
        return $queryBuilder;
    }


}
