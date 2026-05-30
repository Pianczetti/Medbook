<?php

declare(strict_types=1);

namespace MedBook\Booking\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Grid\Query\AbstractDoctrineQueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\DoctrineSearchCriteriaApplicatorInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

final class DepositRuleQueryBuilder extends AbstractDoctrineQueryBuilder
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
            ->select('dr.id_deposit_rule AS id_deposit_rule')
            ->addSelect('IFNULL(rl.name, "Global") AS resource_name')
            ->addSelect('dr.deposit_type AS deposit_type')
            ->addSelect('dr.deposit_value AS deposit_value')
            ->addSelect('dr.is_active AS is_active');

        $this->criteriaApplicator->applyPagination($searchCriteria, $qb);

        $qb->orderBy(
            $searchCriteria->getOrderBy() ?: 'id_deposit_rule',
            $searchCriteria->getOrderWay() ?: 'ASC'
        );

        return $qb;
    }

    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        return $this->getBaseQuery($searchCriteria->getFilters())
            ->select('COUNT(dr.id_deposit_rule)');
    }

    private function getBaseQuery(array $filters): QueryBuilder
    {
        $qb = $this->connection->createQueryBuilder()
            ->from($this->dbPrefix . 'medbook_deposit_rule', 'dr')
            ->leftJoin(
                'dr',
                $this->dbPrefix . 'medbook_resource_lang',
                'rl',
                'dr.id_resource = rl.id_resource AND rl.id_lang = :id_lang'
            )
            ->setParameter('id_lang', $this->languageContext->getId());

        if (isset($filters['id_deposit_rule']) && '' !== $filters['id_deposit_rule']) {
            $qb->andWhere('dr.id_deposit_rule = :id_deposit_rule')
                ->setParameter('id_deposit_rule', (int) $filters['id_deposit_rule']);
        }

        if (!empty($filters['resource_name'])) {
            $qb->andWhere('rl.name LIKE :resource_name')
                ->setParameter('resource_name', '%' . $filters['resource_name'] . '%');
        }

        if (isset($filters['is_active']) && '' !== $filters['is_active']) {
            $qb->andWhere('dr.is_active = :is_active')
                ->setParameter('is_active', (int) $filters['is_active']);
        }

        return $qb;
    }
}
