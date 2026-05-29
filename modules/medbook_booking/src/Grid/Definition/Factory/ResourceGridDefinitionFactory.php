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
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BulkActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ToggleColumn;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\AbstractGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use PrestaShopBundle\Form\Admin\Type\YesAndNoChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class ResourceGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    public const GRID_ID = 'medbook_resource';

    protected function getId(): string
    {
        return self::GRID_ID;
    }

    protected function getName(): string
    {
        return $this->trans('Resources', [], 'Modules.Medbookbooking.Admin');
    }

    protected function getColumns(): ColumnCollection
    {
        return (new ColumnCollection())
            ->add((new BulkActionColumn('bulk'))
                ->setOptions(['bulk_field' => 'id_resource'])
            )
            ->add((new DataColumn('id_resource'))
                ->setName($this->trans('ID', [], 'Admin.Global'))
                ->setOptions(['field' => 'id_resource'])
            )
            ->add((new DataColumn('name'))
                ->setName($this->trans('Name', [], 'Admin.Global'))
                ->setOptions(['field' => 'name'])
            )
            ->add((new DataColumn('resource_type'))
                ->setName($this->trans('Type', [], 'Modules.Medbookbooking.Admin'))
                ->setOptions(['field' => 'resource_type'])
            )
            ->add((new DataColumn('capacity'))
                ->setName($this->trans('Capacity', [], 'Modules.Medbookbooking.Admin'))
                ->setOptions(['field' => 'capacity'])
            )
            ->add((new DataColumn('duration_minutes'))
                ->setName($this->trans('Duration (min)', [], 'Modules.Medbookbooking.Admin'))
                ->setOptions(['field' => 'duration_minutes'])
            )
            ->add((new ToggleColumn('is_active'))
                ->setName($this->trans('Active', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'is_active',
                    'primary_field' => 'id_resource',
                    'route' => 'admin_medbook_booking_resource_toggle_active',
                    'route_param_name' => 'resourceId',
                ])
            )
            ->add((new ActionColumn('actions'))
                ->setName($this->trans('Actions', [], 'Admin.Global'))
                ->setOptions([
                    'actions' => (new RowActionCollection())
                        ->add((new LinkRowAction('edit'))
                            ->setIcon('edit')
                            ->setOptions([
                                'route' => 'admin_medbook_booking_resource_edit',
                                'route_param_name' => 'resourceId',
                                'route_param_field' => 'id_resource',
                            ])
                        )
                        ->add((new LinkRowAction('delete'))
                            ->setIcon('delete')
                            ->setOptions([
                                'route' => 'admin_medbook_booking_resource_delete',
                                'route_param_name' => 'resourceId',
                                'route_param_field' => 'id_resource',
                            ])
                        ),
                ])
            );
    }

    protected function getFilters(): FilterCollection
    {
        return (new FilterCollection())
            ->add(
                (new Filter('id_resource', TextType::class))
                    ->setTypeOptions(['required' => false])
                    ->setAssociatedColumn('id_resource')
            )
            ->add(
                (new Filter('name', TextType::class))
                    ->setTypeOptions(['required' => false])
                    ->setAssociatedColumn('name')
            )
            ->add(
                (new Filter('resource_type', ChoiceType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'choices' => [
                            $this->trans('Doctor', [], 'Modules.Medbookbooking.Admin') => 'doctor',
                            $this->trans('Table', [], 'Modules.Medbookbooking.Admin') => 'table',
                            $this->trans('Service', [], 'Modules.Medbookbooking.Admin') => 'service',
                            $this->trans('Custom', [], 'Modules.Medbookbooking.Admin') => 'custom',
                        ],
                    ])
                    ->setAssociatedColumn('resource_type')
            )
            ->add(
                (new Filter('is_active', YesAndNoChoiceType::class))
                    ->setAssociatedColumn('is_active')
            )
            ->add(
                (new Filter('actions', SearchAndResetType::class))
                    ->setTypeOptions([
                        'reset_route' => 'admin_common_reset_search_by_filter_id',
                        'reset_route_params' => ['filterId' => self::GRID_ID],
                        'redirect_route' => 'admin_medbook_booking_resource_index',
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
