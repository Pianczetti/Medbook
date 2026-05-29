<?php

declare(strict_types=1);

namespace MedBook\Booking\Grid\Definition\Factory;

use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\BulkActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\Type\ButtonBulkAction;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ToggleColumn;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\AbstractGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class ScheduleGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    public const GRID_ID = 'medbook_schedule';

    protected function getId(): string
    {
        return self::GRID_ID;
    }

    protected function getName(): string
    {
        return $this->trans('Schedules', [], 'Modules.Medbookbooking.Admin');
    }

    protected function getColumns(): ColumnCollection
    {
        return (new ColumnCollection())
            ->add((new DataColumn('id_schedule'))
                ->setName($this->trans('ID', [], 'Admin.Global'))
                ->setOptions(['field' => 'id_schedule'])
            )
            ->add((new DataColumn('resource_name'))
                ->setName($this->trans('Resource', [], 'Modules.Medbookbooking.Admin'))
                ->setOptions(['field' => 'resource_name'])
            )
            ->add((new DataColumn('day_of_week'))
                ->setName($this->trans('Day of week', [], 'Modules.Medbookbooking.Admin'))
                ->setOptions(['field' => 'day_of_week'])
            )
            ->add((new DataColumn('specific_date'))
                ->setName($this->trans('Specific date', [], 'Modules.Medbookbooking.Admin'))
                ->setOptions(['field' => 'specific_date'])
            )
            ->add((new DataColumn('time_start'))
                ->setName($this->trans('Start', [], 'Modules.Medbookbooking.Admin'))
                ->setOptions(['field' => 'time_start'])
            )
            ->add((new DataColumn('time_end'))
                ->setName($this->trans('End', [], 'Modules.Medbookbooking.Admin'))
                ->setOptions(['field' => 'time_end'])
            )
            ->add((new ToggleColumn('is_available'))
                ->setName($this->trans('Available', [], 'Modules.Medbookbooking.Admin'))
                ->setOptions([
                    'field' => 'is_available',
                    'primary_field' => 'id_schedule',
                    'route' => 'admin_medbook_booking_schedule_index',
                    'route_param_name' => 'scheduleId',
                ])
            )
            ->add((new ActionColumn('actions'))
                ->setName($this->trans('Actions', [], 'Admin.Global'))
                ->setOptions([
                    'actions' => (new RowActionCollection())
                        ->add((new LinkRowAction('edit'))
                            ->setIcon('edit')
                            ->setOptions([
                                'route' => 'admin_medbook_booking_schedule_edit',
                                'route_param_name' => 'scheduleId',
                                'route_param_field' => 'id_schedule',
                            ])
                        )
                        ->add((new LinkRowAction('delete'))
                            ->setIcon('delete')
                            ->setOptions([
                                'route' => 'admin_medbook_booking_schedule_delete',
                                'route_param_name' => 'scheduleId',
                                'route_param_field' => 'id_schedule',
                            ])
                        ),
                ])
            );
    }

    protected function getFilters(): FilterCollection
    {
        return (new FilterCollection())
            ->add(
                (new Filter('id_schedule', TextType::class))
                    ->setTypeOptions(['required' => false])
                    ->setAssociatedColumn('id_schedule')
            )
            ->add(
                (new Filter('resource_name', TextType::class))
                    ->setTypeOptions(['required' => false])
                    ->setAssociatedColumn('resource_name')
            )
            ->add(
                (new Filter('actions', SearchAndResetType::class))
                    ->setTypeOptions([
                        'reset_route' => 'admin_common_reset_search_by_filter_id',
                        'reset_route_params' => ['filterId' => self::GRID_ID],
                        'redirect_route' => 'admin_medbook_booking_schedule_index',
                    ])
                    ->setAssociatedColumn('actions')
            );
    }

    protected function getGridActions(): GridActionCollection
    {
        return new GridActionCollection();
    }

    protected function getBulkActions(): BulkActionCollection
    {
        return (new BulkActionCollection())
            ->add((new ButtonBulkAction('delete_selection'))
                ->setName($this->trans('Delete selected', [], 'Admin.Actions'))
                ->setOptions([
                    'confirm_message' => $this->trans('Delete selected items?', [], 'Admin.Notifications.Warning'),
                ])
            );
    }
}
