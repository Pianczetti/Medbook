<?php

declare(strict_types=1);

namespace MedBook\Booking\Grid\Filters;

use MedBook\Booking\Grid\Definition\Factory\SpecializationGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Search\Filters;

final class SpecializationFilters extends Filters
{
    /** @var string */
    protected $filterId = SpecializationGridDefinitionFactory::GRID_ID;

    public static function getDefaults(): array
    {
        return [
            'limit' => 25,
            'offset' => 0,
            'orderBy' => 'position',
            'sortOrder' => 'ASC',
            'filters' => [],
        ];
    }
}
