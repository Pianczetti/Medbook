<?php

declare(strict_types=1);

namespace MedBook\Booking\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use MedBook\Booking\Entity\BlockedDate;

final class BlockedDateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BlockedDate::class);
    }
}
