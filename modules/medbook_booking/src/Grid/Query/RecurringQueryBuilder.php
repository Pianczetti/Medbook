<?php

declare(strict_types=1);

namespace MedBook\Booking\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Grid\Query\AbstractDoctrineQueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\DoctrineSearchCriteriaApplicatorInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

final class RecurringQueryBuilder extends AbstractDoctrineQueryBuilder
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
            ->select('sv.id_suggested_visit AS id_suggested_visit')
            ->addSelect('b.customer_name AS customer_name')
            ->addSelect('rl.name AS resource_name')
            ->addSelect('sv.suggested_date_from AS suggested_date_from')
            ->addSelect('sv.suggested_date_to AS suggested_date_to')
            ->addSelect('sv.status AS status');

        $this->criteriaApplicator->applyPagination($searchCriteria, $qb);

        $qb->orderBy(
            $searchCriteria->getOrderBy() ?: 'id_suggested_visit',
            $searchCriteria->getOrderWay() ?: 'DESC'
        );

        return $qb;
    }

    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        return $this->getBaseQuery($searchCriteria->getFilters())
            ->select('COUNT(sv.id_suggested_visit)');
    }

    private function getBaseQuery(array $filters): QueryBuilder
    {
        $qb = $this->connection->createQueryBuilder()
            ->from($this->dbPrefix . 'medbook_suggested_visit', 'sv')
            ->leftJoin(
                'sv',
                $this->dbPrefix . 'medbook_resource_lang',
                'rl',
                'sv.id_resource = rl.id_resource AND rl.id_lang = :id_lang'
            )
            ->leftJoin(
                'sv',
                $this->dbPrefix . 'medbook_booking',
                'b',
                'sv.id_booking = b.id_booking'
            )
            ->setParameter('id_lang', $this->languageContext->getId());

        if (isset($filters['id_suggested_visit']) && '' !== $filters['id_suggested_visit']) {
            $qb->andWhere('sv.id_suggested_visit = :id_suggested_visit')
                ->setParameter('id_suggested_visit', (int) $filters['id_suggested_visit']);
        }

        if (!empty($filters['customer_name'])) {
            $qb->andWhere('b.customer_name LIKE :customer_name')
                ->setParameter('customer_name', '%' . $filters['customer_name'] . '%');
        }

        if (isset($filters['status']) && '' !== $filters['status']) {
            $qb->andWhere('sv.status = :status')
                ->setParameter('status', $filters['status']);
        }

        return $qb;
    }
}
