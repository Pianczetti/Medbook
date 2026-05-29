<?php

declare(strict_types=1);

namespace MedBook\Booking\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use MedBook\Booking\Entity\PriceRule;

final class PriceRuleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PriceRule::class);
    }
}
