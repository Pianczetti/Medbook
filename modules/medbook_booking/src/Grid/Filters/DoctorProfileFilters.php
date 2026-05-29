<?php

declare(strict_types=1);

namespace MedBook\Booking\Grid\Filters;

use MedBook\Booking\Grid\Definition\Factory\DoctorProfileGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Search\Filters;

final class DoctorProfileFilters extends Filters
{
    /** @var string */
    protected $filterId = DoctorProfileGridDefinitionFactory::GRID_ID;

    public static function getDefaults(): array
    {
        return [
            'limit' => 25,
            'offset' => 0,
            'orderBy' => 'id_doctor_profile',
            'sortOrder' => 'ASC',
            'filters' => [],
        ];
    }
}
