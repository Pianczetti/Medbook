<?php

declare(strict_types=1);

namespace MedBook\Booking\Grid\Filters;

use MedBook\Booking\Grid\Definition\Factory\ClinicGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Search\Filters;

final class ClinicFilters extends Filters
{
    /** @var string */
    protected $filterId = ClinicGridDefinitionFactory::GRID_ID;

    public static function getDefaults(): array
    {
        return [
            'limit' => 25,
            'offset' => 0,
            'orderBy' => 'id_clinic',
            'sortOrder' => 'ASC',
            'filters' => [],
        ];
    }
}
