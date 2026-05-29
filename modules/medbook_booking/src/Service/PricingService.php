<?php

declare(strict_types=1);

namespace MedBook\Booking\Service;

use Doctrine\DBAL\Connection;

class PricingService
{
    public function __construct(
        private readonly Connection $connection,
        private readonly string $dbPrefix,
    ) {
    }

    /**
     * Calculate the final price for a slot based on resource base price and applicable price rules.
     */
    public function calculateSlotPrice(int $resourceId, string $date, string $timeStart, string $timeEnd): float
    {
        $basePrice = $this->getResourceBasePrice($resourceId);

        if ($basePrice <= 0) {
            return 0.0;
        }

        $rules = $this->getApplicableRules($resourceId, $date, $timeStart);

        if (empty($rules)) {
            return $basePrice;
        }

        $pricingMode = \Configuration::get('MEDBOOK_BOOKING_PRICING_MODE') ?: 'highest_priority';

        if ($pricingMode === 'highest_priority') {
            // Use only the top-priority rule (first one, already ordered by priority DESC)
            return $this->applyRule($basePrice, $rules[0]);
        }

        // Cumulative: apply all rules sequentially in priority order
        $price = $basePrice;
        foreach ($rules as $rule) {
            $price = $this->applyRule($price, $rule);
        }

        return $price;
    }

    /**
     * Get all active price rules matching a resource, date, time, and day of week.
     *
     * @return array Array of matching rule rows ordered by priority DESC
     */
    public function getApplicableRules(int $resourceId, string $date, string $timeStart): array
    {
        $dateObj = new \DateTime($date);
        $dayOfWeek = (int) $dateObj->format('N');

        $qb = $this->connection->createQueryBuilder();
        $qb->select('pr.*')
            ->from($this->dbPrefix . 'medbook_price_rule', 'pr')
            ->where('(pr.id_resource = :resourceId OR pr.id_resource = 0)')
            ->andWhere('pr.is_active = 1')
            ->andWhere('(pr.date_from IS NULL OR pr.date_from <= :date)')
            ->andWhere('(pr.date_to IS NULL OR pr.date_to >= :date)')
            ->andWhere('(pr.day_of_week IS NULL OR pr.day_of_week = :dayOfWeek)')
            ->andWhere('(pr.time_from IS NULL OR pr.time_from <= :timeStart)')
            ->andWhere('(pr.time_to IS NULL OR pr.time_to >= :timeStart)')
            ->orderBy('pr.priority', 'DESC')
            ->setParameter('resourceId', $resourceId)
            ->setParameter('date', $date)
            ->setParameter('dayOfWeek', $dayOfWeek)
            ->setParameter('timeStart', $timeStart);

        return $qb->executeQuery()->fetchAllAssociative();
    }

    /**
     * Get the base price for a resource.
     */
    public function getResourceBasePrice(int $resourceId): float
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('r.base_price')
            ->from($this->dbPrefix . 'medbook_resource', 'r')
            ->where('r.id_resource = :resourceId')
            ->setParameter('resourceId', $resourceId);

        $result = $qb->executeQuery()->fetchOne();

        return $result !== false ? (float) $result : 0.0;
    }

    /**
     * Apply a single price rule modifier to a price.
     */
    private function applyRule(float $price, array $rule): float
    {
        $value = (float) $rule['modifier_value'];

        if ($rule['modifier_type'] === 'percent') {
            $price += $price * ($value / 100);
        } else {
            // fixed
            $price += $value;
        }

        return max(0.0, round($price, 2));
    }
}
