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

final class ClinicGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    public const GRID_ID = 'medbook_clinic';

    protected function getId(): string
    {
        return self::GRID_ID;
    }

    protected function getName(): string
    {
        return $this->trans('Przychodnie', [], 'Modules.Medbookbooking.Admin');
    }

    protected function getColumns(): ColumnCollection
    {
        return (new ColumnCollection())
            ->add((new BulkActionColumn('bulk'))
                ->setOptions(['bulk_field' => 'id_clinic'])
            )
            ->add((new DataColumn('id_clinic'))
                ->setName($this->trans('ID', [], 'Admin.Global'))
                ->setOptions(['field' => 'id_clinic'])
            )
            ->add((new DataColumn('name'))
                ->setName($this->trans('Nazwa', [], 'Modules.Medbookbooking.Admin'))
                ->setOptions(['field' => 'name'])
            )
            ->add((new DataColumn('city'))
                ->setName($this->trans('Miasto', [], 'Modules.Medbookbooking.Admin'))
                ->setOptions(['field' => 'city'])
            )
            ->add((new DataColumn('address'))
                ->setName($this->trans('Adres', [], 'Modules.Medbookbooking.Admin'))
                ->setOptions(['field' => 'address'])
            )
            ->add((new DataColumn('phone'))
                ->setName($this->trans('Telefon', [], 'Modules.Medbookbooking.Admin'))
                ->setOptions(['field' => 'phone'])
            )
            ->add((new ToggleColumn('is_active'))
                ->setName($this->trans('Active', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'is_active',
                    'primary_field' => 'id_clinic',
                    'route' => 'admin_medbook_booking_clinic_toggle_active',
                    'route_param_name' => 'clinicId',
                ])
            )
            ->add((new ActionColumn('actions'))
                ->setName($this->trans('Actions', [], 'Admin.Global'))
                ->setOptions([
                    'actions' => (new RowActionCollection())
                        ->add((new LinkRowAction('edit'))
                            ->setIcon('edit')
                            ->setOptions([
                                'route' => 'admin_medbook_booking_clinic_edit',
                                'route_param_name' => 'clinicId',
                                'route_param_field' => 'id_clinic',
                            ])
                        )
                        ->add((new LinkRowAction('delete'))
                            ->setIcon('delete')
                            ->setOptions([
                                'route' => 'admin_medbook_booking_clinic_delete',
                                'route_param_name' => 'clinicId',
                                'route_param_field' => 'id_clinic',
                            ])
                        ),
                ])
            );
    }

    protected function getFilters(): FilterCollection
    {
        return (new FilterCollection())
            ->add(
                (new Filter('id_clinic', TextType::class))
                    ->setTypeOptions(['required' => false])
                    ->setAssociatedColumn('id_clinic')
            )
            ->add(
                (new Filter('name', TextType::class))
                    ->setTypeOptions(['required' => false])
                    ->setAssociatedColumn('name')
            )
            ->add(
                (new Filter('city', TextType::class))
                    ->setTypeOptions(['required' => false])
                    ->setAssociatedColumn('city')
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
                        'redirect_route' => 'admin_medbook_booking_clinic_index',
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
