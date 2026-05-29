<?php

declare(strict_types=1);

namespace MedBook\Booking\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Grid\Query\AbstractDoctrineQueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\DoctrineSearchCriteriaApplicatorInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

final class ScheduleQueryBuilder extends AbstractDoctrineQueryBuilder
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
            ->select('s.id_schedule AS id_schedule')
            ->addSelect('rl.name AS resource_name')
            ->addSelect('s.day_of_week AS day_of_week')
            ->addSelect('s.specific_date AS specific_date')
            ->addSelect('s.time_start AS time_start')
            ->addSelect('s.time_end AS time_end')
            ->addSelect('s.is_available AS is_available');

        $this->criteriaApplicator->applyPagination($searchCriteria, $qb);

        $qb->orderBy(
            $searchCriteria->getOrderBy() ?: 'id_schedule',
            $searchCriteria->getOrderWay() ?: 'ASC'
        );

        return $qb;
    }

    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        return $this->getBaseQuery($searchCriteria->getFilters())
            ->select('COUNT(s.id_schedule)');
    }

    private function getBaseQuery(array $filters): QueryBuilder
    {
        $qb = $this->connection->createQueryBuilder()
            ->from($this->dbPrefix . 'medbook_schedule', 's')
            ->leftJoin(
                's',
                $this->dbPrefix . 'medbook_resource_lang',
                'rl',
                's.id_resource = rl.id_resource AND rl.id_lang = :id_lang'
            )
            ->setParameter('id_lang', $this->languageContext->getId());

        if (isset($filters['id_schedule']) && '' !== $filters['id_schedule']) {
            $qb->andWhere('s.id_schedule = :id_schedule')
                ->setParameter('id_schedule', (int) $filters['id_schedule']);
        }

        if (!empty($filters['resource_name'])) {
            $qb->andWhere('rl.name LIKE :resource_name')
                ->setParameter('resource_name', '%' . $filters['resource_name'] . '%');
        }

        return $qb;
    }
}
