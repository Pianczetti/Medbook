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
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class DepositRuleGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    public const GRID_ID = 'medbook_deposit_rule';

    protected function getId(): string
    {
        return self::GRID_ID;
    }

    protected function getName(): string
    {
        return $this->trans('Deposit Rules', [], 'Modules.Medbookbooking.Admin');
    }

    protected function getColumns(): ColumnCollection
    {
        return (new ColumnCollection())
            ->add((new BulkActionColumn('bulk'))
                ->setOptions(['bulk_field' => 'id_deposit_rule'])
            )
            ->add((new DataColumn('id_deposit_rule'))
                ->setName($this->trans('ID', [], 'Admin.Global'))
                ->setOptions(['field' => 'id_deposit_rule'])
            )
            ->add((new DataColumn('resource_name'))
                ->setName($this->trans('Resource', [], 'Modules.Medbookbooking.Admin'))
                ->setOptions(['field' => 'resource_name'])
            )
            ->add((new DataColumn('deposit_type'))
                ->setName($this->trans('Type', [], 'Modules.Medbookbooking.Admin'))
                ->setOptions(['field' => 'deposit_type'])
            )
            ->add((new DataColumn('deposit_value'))
                ->setName($this->trans('Value', [], 'Modules.Medbookbooking.Admin'))
                ->setOptions(['field' => 'deposit_value'])
            )
            ->add((new ToggleColumn('is_active'))
                ->setName($this->trans('Active', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'is_active',
                    'primary_field' => 'id_deposit_rule',
                    'route' => 'admin_medbook_booking_deposit_rule_toggle_active',
                    'route_param_name' => 'depositRuleId',
                ])
            )
            ->add((new ActionColumn('actions'))
                ->setName($this->trans('Actions', [], 'Admin.Global'))
                ->setOptions([
                    'actions' => (new RowActionCollection())
                        ->add((new LinkRowAction('edit'))
                            ->setIcon('edit')
                            ->setOptions([
                                'route' => 'admin_medbook_booking_deposit_rule_edit',
                                'route_param_name' => 'depositRuleId',
                                'route_param_field' => 'id_deposit_rule',
                            ])
                        )
                        ->add((new LinkRowAction('delete'))
                            ->setIcon('delete')
                            ->setOptions([
                                'route' => 'admin_medbook_booking_deposit_rule_delete',
                                'route_param_name' => 'depositRuleId',
                                'route_param_field' => 'id_deposit_rule',
                            ])
                        ),
                ])
            );
    }

    protected function getFilters(): FilterCollection
    {
        return (new FilterCollection())
            ->add(
                (new Filter('id_deposit_rule', TextType::class))
                    ->setTypeOptions(['required' => false])
                    ->setAssociatedColumn('id_deposit_rule')
            )
            ->add(
                (new Filter('resource_name', TextType::class))
                    ->setTypeOptions(['required' => false])
                    ->setAssociatedColumn('resource_name')
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
                        'redirect_route' => 'admin_medbook_booking_deposit_rule_index',
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
