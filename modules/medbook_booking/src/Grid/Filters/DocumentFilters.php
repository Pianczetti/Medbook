<?php

declare(strict_types=1);

namespace MedBook\Booking\Grid\Filters;

use MedBook\Booking\Grid\Definition\Factory\DocumentGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Search\Filters;

final class DocumentFilters extends Filters
{
    /** @var string */
    protected $filterId = DocumentGridDefinitionFactory::GRID_ID;

    public static function getDefaults(): array
    {
        return [
            'limit' => 25,
            'offset' => 0,
            'orderBy' => 'id_document',
            'sortOrder' => 'DESC',
            'filters' => [],
        ];
    }
}
