<?php

declare(strict_types=1);

namespace MedBook\Booking\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Grid\Query\AbstractDoctrineQueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\DoctrineSearchCriteriaApplicatorInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

final class BookingQueryBuilder extends AbstractDoctrineQueryBuilder
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
            ->select('b.id_booking AS id_booking')
            ->addSelect('b.reference_code AS reference_code')
            ->addSelect('rl.name AS resource_name')
            ->addSelect('b.customer_name AS customer_name')
            ->addSelect('b.booking_date AS booking_date')
            ->addSelect('b.time_start AS time_start')
            ->addSelect('b.time_end AS time_end')
            ->addSelect('b.status AS status')
            ->addSelect('b.no_show AS no_show');

        $this->criteriaApplicator->applyPagination($searchCriteria, $qb);

        $qb->orderBy(
            $searchCriteria->getOrderBy() ?: 'id_booking',
            $searchCriteria->getOrderWay() ?: 'DESC'
        );

        return $qb;
    }

    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        return $this->getBaseQuery($searchCriteria->getFilters())
            ->select('COUNT(b.id_booking)');
    }

    private function getBaseQuery(array $filters): QueryBuilder
    {
        $qb = $this->connection->createQueryBuilder()
            ->from($this->dbPrefix . 'medbook_booking', 'b')
            ->leftJoin(
                'b',
                $this->dbPrefix . 'medbook_resource_lang',
                'rl',
                'b.id_resource = rl.id_resource AND rl.id_lang = :id_lang'
            )
            ->setParameter('id_lang', $this->languageContext->getId());

        if (isset($filters['id_booking']) && '' !== $filters['id_booking']) {
            $qb->andWhere('b.id_booking = :id_booking')
                ->setParameter('id_booking', (int) $filters['id_booking']);
        }

        if (!empty($filters['reference_code'])) {
            $qb->andWhere('b.reference_code LIKE :reference_code')
                ->setParameter('reference_code', '%' . $filters['reference_code'] . '%');
        }

        if (!empty($filters['customer_name'])) {
            $qb->andWhere('b.customer_name LIKE :customer_name')
                ->setParameter('customer_name', '%' . $filters['customer_name'] . '%');
        }

        if (isset($filters['status']) && '' !== $filters['status']) {
            $qb->andWhere('b.status = :status')
                ->setParameter('status', $filters['status']);
        }

        return $qb;
    }
}
