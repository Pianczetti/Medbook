<?php

declare(strict_types=1);

namespace MedBook\Booking\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\AbstractDoctrineQueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\DoctrineSearchCriteriaApplicatorInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

final class DocumentQueryBuilder extends AbstractDoctrineQueryBuilder
{
    public function __construct(
        Connection $connection,
        string $dbPrefix,
        private readonly DoctrineSearchCriteriaApplicatorInterface $criteriaApplicator,
    ) {
        parent::__construct($connection, $dbPrefix);
    }

    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $qb = $this->getBaseQuery($searchCriteria->getFilters())
            ->select('d.id_document AS id_document')
            ->addSelect('d.id_customer AS id_customer')
            ->addSelect('d.id_booking AS id_booking')
            ->addSelect('d.document_type AS document_type')
            ->addSelect('d.original_name AS original_name')
            ->addSelect('d.uploaded_by AS uploaded_by')
            ->addSelect('d.date_add AS date_add');

        $this->criteriaApplicator->applyPagination($searchCriteria, $qb);

        $qb->orderBy(
            $searchCriteria->getOrderBy() ?: 'id_document',
            $searchCriteria->getOrderWay() ?: 'DESC'
        );

        return $qb;
    }

    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        return $this->getBaseQuery($searchCriteria->getFilters())
            ->select('COUNT(d.id_document)');
    }

    private function getBaseQuery(array $filters): QueryBuilder
    {
        $qb = $this->connection->createQueryBuilder()
            ->from($this->dbPrefix . 'medbook_document', 'd');

        if (isset($filters['id_document']) && '' !== $filters['id_document']) {
            $qb->andWhere('d.id_document = :id_document')
                ->setParameter('id_document', (int) $filters['id_document']);
        }

        if (!empty($filters['original_name'])) {
            $qb->andWhere('d.original_name LIKE :original_name')
                ->setParameter('original_name', '%' . $filters['original_name'] . '%');
        }

        if (isset($filters['document_type']) && '' !== $filters['document_type']) {
            $qb->andWhere('d.document_type = :document_type')
                ->setParameter('document_type', $filters['document_type']);
        }

        return $qb;
    }
}
