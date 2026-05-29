<?php

declare(strict_types=1);

namespace MedBook\Booking\Grid\Filters;

use PrestaShop\PrestaShop\Core\Search\Filters;
use MedBook\Booking\Grid\Definition\Factory\RecurringGridDefinitionFactory;

final class RecurringFilters extends Filters
{
    /** @var string */
    protected $filterId = RecurringGridDefinitionFactory::GRID_ID;

    public static function getDefaults(): array
    {
        return [
            'limit' => 25,
            'offset' => 0,
            'orderBy' => 'id_suggested_visit',
            'sortOrder' => 'DESC',
            'filters' => [],
        ];
    }
}
