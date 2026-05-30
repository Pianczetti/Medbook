<?php

declare(strict_types=1);

namespace MedBook\Booking\Grid\Filters;

use PrestaShop\PrestaShop\Core\Search\Filters;
use MedBook\Booking\Grid\Definition\Factory\RefundRuleGridDefinitionFactory;

final class RefundRuleFilters extends Filters
{
    /** @var string */
    protected $filterId = RefundRuleGridDefinitionFactory::GRID_ID;

    public static function getDefaults(): array
    {
        return [
            'limit' => 25,
            'offset' => 0,
            'orderBy' => 'id_refund_rule',
            'sortOrder' => 'ASC',
            'filters' => [],
        ];
    }
}
