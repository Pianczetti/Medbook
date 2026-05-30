<?php

declare(strict_types=1);

namespace MedBook\Booking\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Grid\Query\AbstractDoctrineQueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\DoctrineSearchCriteriaApplicatorInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

final class BlockedDateQueryBuilder extends AbstractDoctrineQueryBuilder
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
            ->select('bd.id_blocked_date AS id_blocked_date')
            ->addSelect('rl.name AS resource_name')
            ->addSelect('bd.blocked_date AS blocked_date')
            ->addSelect('bd.reason AS reason');

        $this->criteriaApplicator->applyPagination($searchCriteria, $qb);

        $qb->orderBy(
            $searchCriteria->getOrderBy() ?: 'id_blocked_date',
            $searchCriteria->getOrderWay() ?: 'DESC'
        );

        return $qb;
    }

    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        return $this->getBaseQuery($searchCriteria->getFilters())
            ->select('COUNT(bd.id_blocked_date)');
    }

    private function getBaseQuery(array $filters): QueryBuilder
    {
        $qb = $this->connection->createQueryBuilder()
            ->from($this->dbPrefix . 'medbook_blocked_date', 'bd')
            ->leftJoin(
                'bd',
                $this->dbPrefix . 'medbook_resource_lang',
                'rl',
                'bd.id_resource = rl.id_resource AND rl.id_lang = :id_lang'
            )
            ->setParameter('id_lang', $this->languageContext->getId());

        if (isset($filters['id_blocked_date']) && '' !== $filters['id_blocked_date']) {
            $qb->andWhere('bd.id_blocked_date = :id_blocked_date')
                ->setParameter('id_blocked_date', (int) $filters['id_blocked_date']);
        }

        if (!empty($filters['reason'])) {
            $qb->andWhere('bd.reason LIKE :reason')
                ->setParameter('reason', '%' . $filters['reason'] . '%');
        }

        return $qb;
    }
}
