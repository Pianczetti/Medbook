<?php

declare(strict_types=1);

namespace MedBook\Booking\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Grid\Query\AbstractDoctrineQueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\DoctrineSearchCriteriaApplicatorInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

final class AddonQueryBuilder extends AbstractDoctrineQueryBuilder
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
            ->select('a.id_addon AS id_addon')
            ->addSelect('IFNULL(al.name, "--") AS name')
            ->addSelect('IFNULL(rl.name, "All") AS resource_name')
            ->addSelect('a.price AS price')
            ->addSelect('a.is_active AS is_active')
            ->addSelect('a.position AS position');

        $this->criteriaApplicator->applyPagination($searchCriteria, $qb);

        $qb->orderBy(
            $searchCriteria->getOrderBy() ?: 'id_addon',
            $searchCriteria->getOrderWay() ?: 'ASC'
        );

        return $qb;
    }

    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        return $this->getBaseQuery($searchCriteria->getFilters())
            ->select('COUNT(a.id_addon)');
    }

    private function getBaseQuery(array $filters): QueryBuilder
    {
        $qb = $this->connection->createQueryBuilder()
            ->from($this->dbPrefix . 'medbook_addon', 'a')
            ->leftJoin(
                'a',
                $this->dbPrefix . 'medbook_addon_lang',
                'al',
                'a.id_addon = al.id_addon AND al.id_lang = :id_lang'
            )
            ->leftJoin(
                'a',
                $this->dbPrefix . 'medbook_resource_lang',
                'rl',
                'a.id_resource = rl.id_resource AND rl.id_lang = :id_lang'
            )
            ->setParameter('id_lang', $this->languageContext->getId());

        if (isset($filters['id_addon']) && '' !== $filters['id_addon']) {
            $qb->andWhere('a.id_addon = :id_addon')
                ->setParameter('id_addon', (int) $filters['id_addon']);
        }

        if (!empty($filters['name'])) {
            $qb->andWhere('al.name LIKE :name')
                ->setParameter('name', '%' . $filters['name'] . '%');
        }

        if (isset($filters['is_active']) && '' !== $filters['is_active']) {
            $qb->andWhere('a.is_active = :is_active')
                ->setParameter('is_active', (int) $filters['is_active']);
        }

        return $qb;
    }
}
