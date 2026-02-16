<?php

namespace App\Repository;

use App\Entity\Hardware;
use Doctrine\Persistence\ManagerRegistry;


class HardwareRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Hardware::class);
    }


}
