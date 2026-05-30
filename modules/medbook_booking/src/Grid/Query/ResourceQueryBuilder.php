<?php

declare(strict_types=1);

namespace MedBook\Booking\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Grid\Query\AbstractDoctrineQueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\DoctrineSearchCriteriaApplicatorInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

final class ResourceQueryBuilder extends AbstractDoctrineQueryBuilder
{
    public function __construct(
        Connection $connection,
        string $dbPrefix,
        private readonly DoctrineSearchCriteriaApplicatorInterface $criteriaApplicator,
        private readonly LanguageContext $languageContext,
    ) {
        parent::__construct($connection, $dbPrefix);
    }

    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $qb = $this->getBaseQuery($searchCriteria->getFilters())
            ->select('r.id_resource AS id_resource')
            ->addSelect('rl.name AS name')
            ->addSelect('r.resource_type AS resource_type')
            ->addSelect('r.capacity AS capacity')
            ->addSelect('r.duration_minutes AS duration_minutes')
            ->addSelect('r.is_active AS is_active')
            ->addSelect('r.id_product AS id_product');

        $this->criteriaApplicator->applyPagination($searchCriteria, $qb);

        $qb->orderBy(
            $searchCriteria->getOrderBy() ?: 'id_resource',
            $searchCriteria->getOrderWay() ?: 'ASC'
        );

        return $qb;
    }

    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        return $this->getBaseQuery($searchCriteria->getFilters())
            ->select('COUNT(r.id_resource)');
    }

    private function getBaseQuery(array $filters): QueryBuilder
    {
        $qb = $this->connection->createQueryBuilder()
            ->from($this->dbPrefix . 'medbook_resource', 'r')
            ->leftJoin(
                'r',
                $this->dbPrefix . 'medbook_resource_lang',
                'rl',
                'r.id_resource = rl.id_resource AND rl.id_lang = :id_lang'
            )
            ->setParameter('id_lang', $this->languageContext->getId());

        if (isset($filters['id_resource']) && '' !== $filters['id_resource']) {
            $qb->andWhere('r.id_resource = :id_resource')
                ->setParameter('id_resource', (int) $filters['id_resource']);
        }

        if (!empty($filters['name'])) {
            $qb->andWhere('rl.name LIKE :name')
                ->setParameter('name', '%' . $filters['name'] . '%');
        }

        if (isset($filters['resource_type']) && '' !== $filters['resource_type']) {
            $qb->andWhere('r.resource_type = :resource_type')
                ->setParameter('resource_type', $filters['resource_type']);
        }

        if (isset($filters['is_active']) && '' !== $filters['is_active']) {
            $qb->andWhere('r.is_active = :is_active')
                ->setParameter('is_active', (int) $filters['is_active']);
        }

        return $qb;
    }
}
