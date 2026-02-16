<?php

namespace App\Repository;

use App\Entity\Meilenstein;
use Doctrine\Persistence\ManagerRegistry;

class MeilensteinRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Meilenstein::class);
    }

}
