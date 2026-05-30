<?php

declare(strict_types=1);

namespace MedBook\Booking\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Grid\Query\AbstractDoctrineQueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\DoctrineSearchCriteriaApplicatorInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

final class WaitlistQueryBuilder extends AbstractDoctrineQueryBuilder
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
            ->select('w.id_waitlist AS id_waitlist')
            ->addSelect('w.customer_name AS customer_name')
            ->addSelect('w.customer_email AS customer_email')
            ->addSelect('rl.name AS resource_name')
            ->addSelect('w.is_priority AS is_priority')
            ->addSelect('w.status AS status')
            ->addSelect('w.date_add AS date_add');

        $this->criteriaApplicator->applyPagination($searchCriteria, $qb);

        $qb->orderBy(
            $searchCriteria->getOrderBy() ?: 'id_waitlist',
            $searchCriteria->getOrderWay() ?: 'DESC'
        );

        return $qb;
    }

    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        return $this->getBaseQuery($searchCriteria->getFilters())
            ->select('COUNT(w.id_waitlist)');
    }

    private function getBaseQuery(array $filters): QueryBuilder
    {
        $qb = $this->connection->createQueryBuilder()
            ->from($this->dbPrefix . 'medbook_waitlist', 'w')
            ->leftJoin(
                'w',
                $this->dbPrefix . 'medbook_resource_lang',
                'rl',
                'w.id_resource = rl.id_resource AND rl.id_lang = :id_lang'
            )
            ->setParameter('id_lang', $this->languageContext->getId());

        if (isset($filters['id_waitlist']) && '' !== $filters['id_waitlist']) {
            $qb->andWhere('w.id_waitlist = :id_waitlist')
                ->setParameter('id_waitlist', (int) $filters['id_waitlist']);
        }

        if (!empty($filters['customer_name'])) {
            $qb->andWhere('w.customer_name LIKE :customer_name')
                ->setParameter('customer_name', '%' . $filters['customer_name'] . '%');
        }

        if (!empty($filters['customer_email'])) {
            $qb->andWhere('w.customer_email LIKE :customer_email')
                ->setParameter('customer_email', '%' . $filters['customer_email'] . '%');
        }

        if (isset($filters['status']) && '' !== $filters['status']) {
            $qb->andWhere('w.status = :status')
                ->setParameter('status', $filters['status']);
        }

        return $qb;
    }
}
