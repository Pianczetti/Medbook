<?php

declare(strict_types=1);

namespace MedBook\Booking\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use MedBook\Booking\Entity\ConfirmToken;

final class ConfirmTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ConfirmToken::class);
    }
}
