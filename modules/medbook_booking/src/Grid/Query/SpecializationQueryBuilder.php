<?php

declare(strict_types=1);

namespace MedBook\Booking\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\AbstractDoctrineQueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\DoctrineSearchCriteriaApplicatorInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

final class SpecializationQueryBuilder extends AbstractDoctrineQueryBuilder
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
            ->select('s.id_specialization AS id_specialization')
            ->addSelect('s.name AS name')
            ->addSelect('s.slug AS slug')
            ->addSelect('s.icon AS icon')
            ->addSelect('s.is_active AS is_active')
            ->addSelect('s.position AS position');

        $this->criteriaApplicator->applyPagination($searchCriteria, $qb);

        $qb->orderBy(
            $searchCriteria->getOrderBy() ?: 'position',
            $searchCriteria->getOrderWay() ?: 'ASC'
        );

        return $qb;
    }

    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        return $this->getBaseQuery($searchCriteria->getFilters())
            ->select('COUNT(s.id_specialization)');
    }

    private function getBaseQuery(array $filters): QueryBuilder
    {
        $qb = $this->connection->createQueryBuilder()
            ->from($this->dbPrefix . 'medbook_specialization', 's');

        if (isset($filters['id_specialization']) && '' !== $filters['id_specialization']) {
            $qb->andWhere('s.id_specialization = :id_specialization')
                ->setParameter('id_specialization', (int) $filters['id_specialization']);
        }

        if (!empty($filters['name'])) {
            $qb->andWhere('s.name LIKE :name')
                ->setParameter('name', '%' . $filters['name'] . '%');
        }

        if (isset($filters['is_active']) && '' !== $filters['is_active']) {
            $qb->andWhere('s.is_active = :is_active')
                ->setParameter('is_active', (int) $filters['is_active']);
        }

        return $qb;
    }
}
