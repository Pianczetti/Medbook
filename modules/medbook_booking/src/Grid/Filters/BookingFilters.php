<?php

declare(strict_types=1);

namespace MedBook\Booking\Grid\Filters;

use PrestaShop\PrestaShop\Core\Search\Filters;
use MedBook\Booking\Grid\Definition\Factory\BookingGridDefinitionFactory;

final class BookingFilters extends Filters
{
    /** @var string */
    protected $filterId = BookingGridDefinitionFactory::GRID_ID;

    public static function getDefaults(): array
    {
        return [
            'limit' => 25,
            'offset' => 0,
            'orderBy' => 'id_booking',
            'sortOrder' => 'DESC',
            'filters' => [],
        ];
    }
}
