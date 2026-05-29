<?php

declare(strict_types=1);

namespace MedBook\Booking\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Grid\Query\AbstractDoctrineQueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\DoctrineSearchCriteriaApplicatorInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

final class RefundRuleQueryBuilder extends AbstractDoctrineQueryBuilder
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
            ->select('rr.id_refund_rule AS id_refund_rule')
            ->addSelect('IFNULL(rl.name, "Global") AS resource_name')
            ->addSelect('rr.hours_before AS hours_before')
            ->addSelect('rr.refund_percent AS refund_percent');

        $this->criteriaApplicator->applyPagination($searchCriteria, $qb);

        $qb->orderBy(
            $searchCriteria->getOrderBy() ?: 'id_refund_rule',
            $searchCriteria->getOrderWay() ?: 'ASC'
        );

        return $qb;
    }

    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        return $this->getBaseQuery($searchCriteria->getFilters())
            ->select('COUNT(rr.id_refund_rule)');
    }

    private function getBaseQuery(array $filters): QueryBuilder
    {
        $qb = $this->connection->createQueryBuilder()
            ->from($this->dbPrefix . 'medbook_refund_rule', 'rr')
            ->leftJoin(
                'rr',
                $this->dbPrefix . 'medbook_resource_lang',
                'rl',
                'rr.id_resource = rl.id_resource AND rl.id_lang = :id_lang'
            )
            ->setParameter('id_lang', $this->languageContext->getId());

        if (isset($filters['id_refund_rule']) && '' !== $filters['id_refund_rule']) {
            $qb->andWhere('rr.id_refund_rule = :id_refund_rule')
                ->setParameter('id_refund_rule', (int) $filters['id_refund_rule']);
        }

        if (!empty($filters['resource_name'])) {
            $qb->andWhere('rl.name LIKE :resource_name')
                ->setParameter('resource_name', '%' . $filters['resource_name'] . '%');
        }

        return $qb;
    }
}
