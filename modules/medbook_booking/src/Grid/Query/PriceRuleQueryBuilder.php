<?php

declare(strict_types=1);

namespace MedBook\Booking\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Grid\Query\AbstractDoctrineQueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\DoctrineSearchCriteriaApplicatorInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

final class PriceRuleQueryBuilder extends AbstractDoctrineQueryBuilder
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
            ->select('pr.id_price_rule AS id_price_rule')
            ->addSelect('IFNULL(rl.name, "--") AS resource_name')
            ->addSelect('pr.name AS name')
            ->addSelect('pr.day_of_week AS day_of_week')
            ->addSelect('pr.modifier_type AS modifier_type')
            ->addSelect('pr.modifier_value AS modifier_value')
            ->addSelect('pr.priority AS priority')
            ->addSelect('pr.is_active AS is_active');

        $this->criteriaApplicator->applyPagination($searchCriteria, $qb);

        $qb->orderBy(
            $searchCriteria->getOrderBy() ?: 'id_price_rule',
            $searchCriteria->getOrderWay() ?: 'ASC'
        );

        return $qb;
    }

    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        return $this->getBaseQuery($searchCriteria->getFilters())
            ->select('COUNT(pr.id_price_rule)');
    }

    private function getBaseQuery(array $filters): QueryBuilder
    {
        $qb = $this->connection->createQueryBuilder()
            ->from($this->dbPrefix . 'medbook_price_rule', 'pr')
            ->leftJoin(
                'pr',
                $this->dbPrefix . 'medbook_resource_lang',
                'rl',
                'pr.id_resource = rl.id_resource AND rl.id_lang = :id_lang'
            )
            ->setParameter('id_lang', $this->languageContext->getId());

        if (isset($filters['id_price_rule']) && '' !== $filters['id_price_rule']) {
            $qb->andWhere('pr.id_price_rule = :id_price_rule')
                ->setParameter('id_price_rule', (int) $filters['id_price_rule']);
        }

        if (!empty($filters['resource_name'])) {
            $qb->andWhere('rl.name LIKE :resource_name')
                ->setParameter('resource_name', '%' . $filters['resource_name'] . '%');
        }

        if (!empty($filters['name'])) {
            $qb->andWhere('pr.name LIKE :name')
                ->setParameter('name', '%' . $filters['name'] . '%');
        }

        if (isset($filters['is_active']) && '' !== $filters['is_active']) {
            $qb->andWhere('pr.is_active = :is_active')
                ->setParameter('is_active', (int) $filters['is_active']);
        }

        return $qb;
    }
}
