<?php

declare(strict_types=1);

namespace MedBook\Booking\Grid\Filters;

use PrestaShop\PrestaShop\Core\Search\Filters;
use MedBook\Booking\Grid\Definition\Factory\BlockedDateGridDefinitionFactory;

final class BlockedDateFilters extends Filters
{
    /** @var string */
    protected $filterId = BlockedDateGridDefinitionFactory::GRID_ID;

    public static function getDefaults(): array
    {
        return [
            'limit' => 25,
            'offset' => 0,
            'orderBy' => 'id_blocked_date',
            'sortOrder' => 'DESC',
            'filters' => [],
        ];
    }
}
