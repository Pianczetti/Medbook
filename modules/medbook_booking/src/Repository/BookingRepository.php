<?php

declare(strict_types=1);

namespace MedBook\Booking\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use MedBook\Booking\Entity\Booking;

final class BookingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Booking::class);
    }

    /**
     * @return Booking[]
     */
    public function findByResourceAndDate(int $resourceId, \DateTimeInterface $date): array
    {
        return $this->createQueryBuilder('b')
            ->where('b.idResource = :resourceId')
            ->andWhere('b.bookingDate = :date')
            ->andWhere('b.status NOT IN (:excludedStatuses)')
            ->setParameter('resourceId', $resourceId)
            ->setParameter('date', $date)
            ->setParameter('excludedStatuses', ['cancelled'])
            ->orderBy('b.timeStart', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Booking[]
     */
    public function findByCustomer(int $customerId): array
    {
        return $this->createQueryBuilder('b')
            ->where('b.idCustomer = :customerId')
            ->setParameter('customerId', $customerId)
            ->orderBy('b.bookingDate', 'DESC')
            ->addOrderBy('b.timeStart', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
