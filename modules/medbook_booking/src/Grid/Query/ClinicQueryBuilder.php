<?php

declare(strict_types=1);

namespace MedBook\Booking\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\AbstractDoctrineQueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\DoctrineSearchCriteriaApplicatorInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

final class ClinicQueryBuilder extends AbstractDoctrineQueryBuilder
{
    public function __construct(
        Connection $connection,
        string $dbPrefix,
        private readonly DoctrineSearchCriteriaApplicatorInterface $criteriaApplicator,
    ) {
        parent::__construct($connection, $dbPrefix);
    }

    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $qb = $this->getBaseQuery($searchCriteria->getFilters())
            ->select('c.id_clinic AS id_clinic')
            ->addSelect('c.name AS name')
            ->addSelect('c.city AS city')
            ->addSelect('c.address AS address')
            ->addSelect('c.phone AS phone')
            ->addSelect('c.email AS email')
            ->addSelect('c.is_active AS is_active');

        $this->criteriaApplicator->applyPagination($searchCriteria, $qb);

        $qb->orderBy(
            $searchCriteria->getOrderBy() ?: 'id_clinic',
            $searchCriteria->getOrderWay() ?: 'ASC'
        );

        return $qb;
    }

    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        return $this->getBaseQuery($searchCriteria->getFilters())
            ->select('COUNT(c.id_clinic)');
    }

    private function getBaseQuery(array $filters): QueryBuilder
    {
        $qb = $this->connection->createQueryBuilder()
            ->from($this->dbPrefix . 'medbook_clinic', 'c');

        if (isset($filters['id_clinic']) && '' !== $filters['id_clinic']) {
            $qb->andWhere('c.id_clinic = :id_clinic')
                ->setParameter('id_clinic', (int) $filters['id_clinic']);
        }

        if (!empty($filters['name'])) {
            $qb->andWhere('c.name LIKE :name')
                ->setParameter('name', '%' . $filters['name'] . '%');
        }

        if (!empty($filters['city'])) {
            $qb->andWhere('c.city LIKE :city')
                ->setParameter('city', '%' . $filters['city'] . '%');
        }

        if (isset($filters['is_active']) && '' !== $filters['is_active']) {
            $qb->andWhere('c.is_active = :is_active')
                ->setParameter('is_active', (int) $filters['is_active']);
        }

        return $qb;
    }
}
