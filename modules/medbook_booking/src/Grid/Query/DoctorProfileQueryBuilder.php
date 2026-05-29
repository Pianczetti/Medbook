<?php

declare(strict_types=1);

namespace MedBook\Booking\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Grid\Query\AbstractDoctrineQueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\DoctrineSearchCriteriaApplicatorInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

final class DoctorProfileQueryBuilder extends AbstractDoctrineQueryBuilder
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
            ->select('dp.id_doctor_profile AS id_doctor_profile')
            ->addSelect('rl.name AS resource_name')
            ->addSelect('s.name AS specialization_name')
            ->addSelect('dp.pwz_number AS pwz_number')
            ->addSelect('dp.experience_years AS experience_years')
            ->addSelect('dp.consultation_online AS consultation_online')
            ->addSelect('dp.consultation_inperson AS consultation_inperson');

        $this->criteriaApplicator->applyPagination($searchCriteria, $qb);

        $qb->orderBy(
            $searchCriteria->getOrderBy() ?: 'id_doctor_profile',
            $searchCriteria->getOrderWay() ?: 'ASC'
        );

        return $qb;
    }

    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        return $this->getBaseQuery($searchCriteria->getFilters())
            ->select('COUNT(dp.id_doctor_profile)');
    }

    private function getBaseQuery(array $filters): QueryBuilder
    {
        $qb = $this->connection->createQueryBuilder()
            ->from($this->dbPrefix . 'medbook_doctor_profile', 'dp')
            ->leftJoin(
                'dp',
                $this->dbPrefix . 'medbook_resource_lang',
                'rl',
                'dp.id_resource = rl.id_resource AND rl.id_lang = :id_lang'
            )
            ->leftJoin(
                'dp',
                $this->dbPrefix . 'medbook_specialization',
                's',
                'dp.id_specialization = s.id_specialization'
            )
            ->setParameter('id_lang', $this->languageContext->getId());

        if (isset($filters['id_doctor_profile']) && '' !== $filters['id_doctor_profile']) {
            $qb->andWhere('dp.id_doctor_profile = :id_doctor_profile')
                ->setParameter('id_doctor_profile', (int) $filters['id_doctor_profile']);
        }

        if (!empty($filters['resource_name'])) {
            $qb->andWhere('rl.name LIKE :resource_name')
                ->setParameter('resource_name', '%' . $filters['resource_name'] . '%');
        }

        if (!empty($filters['specialization_name'])) {
            $qb->andWhere('s.name LIKE :specialization_name')
                ->setParameter('specialization_name', '%' . $filters['specialization_name'] . '%');
        }

        return $qb;
    }
}
