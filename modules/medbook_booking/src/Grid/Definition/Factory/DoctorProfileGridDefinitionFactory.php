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
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\AbstractGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class DoctorProfileGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    public const GRID_ID = 'medbook_doctor_profile';

    protected function getId(): string
    {
        return self::GRID_ID;
    }

    protected function getName(): string
    {
        return $this->trans('Profile lekarzy', [], 'Modules.Medbookbooking.Admin');
    }

    protected function getColumns(): ColumnCollection
    {
        return (new ColumnCollection())
            ->add((new BulkActionColumn('bulk'))
                ->setOptions(['bulk_field' => 'id_doctor_profile'])
            )
            ->add((new DataColumn('id_doctor_profile'))
                ->setName($this->trans('ID', [], 'Admin.Global'))
                ->setOptions(['field' => 'id_doctor_profile'])
            )
            ->add((new DataColumn('resource_name'))
                ->setName($this->trans('Lekarz', [], 'Modules.Medbookbooking.Admin'))
                ->setOptions(['field' => 'resource_name'])
            )
            ->add((new DataColumn('specialization_name'))
                ->setName($this->trans('Specjalizacja', [], 'Modules.Medbookbooking.Admin'))
                ->setOptions(['field' => 'specialization_name'])
            )
            ->add((new DataColumn('pwz_number'))
                ->setName($this->trans('Numer PWZ', [], 'Modules.Medbookbooking.Admin'))
                ->setOptions(['field' => 'pwz_number'])
            )
            ->add((new DataColumn('experience_years'))
                ->setName($this->trans('Lata doswiadczenia', [], 'Modules.Medbookbooking.Admin'))
                ->setOptions(['field' => 'experience_years'])
            )
            ->add((new ActionColumn('actions'))
                ->setName($this->trans('Actions', [], 'Admin.Global'))
                ->setOptions([
                    'actions' => (new RowActionCollection())
                        ->add((new LinkRowAction('edit'))
                            ->setIcon('edit')
                            ->setOptions([
                                'route' => 'admin_medbook_booking_doctor_profile_edit',
                                'route_param_name' => 'doctorProfileId',
                                'route_param_field' => 'id_doctor_profile',
                            ])
                        )
                        ->add((new LinkRowAction('delete'))
                            ->setIcon('delete')
                            ->setOptions([
                                'route' => 'admin_medbook_booking_doctor_profile_delete',
                                'route_param_name' => 'doctorProfileId',
                                'route_param_field' => 'id_doctor_profile',
                            ])
                        ),
                ])
            );
    }

    protected function getFilters(): FilterCollection
    {
        return (new FilterCollection())
            ->add(
                (new Filter('id_doctor_profile', TextType::class))
                    ->setTypeOptions(['required' => false])
                    ->setAssociatedColumn('id_doctor_profile')
            )
            ->add(
                (new Filter('resource_name', TextType::class))
                    ->setTypeOptions(['required' => false])
                    ->setAssociatedColumn('resource_name')
            )
            ->add(
                (new Filter('specialization_name', TextType::class))
                    ->setTypeOptions(['required' => false])
                    ->setAssociatedColumn('specialization_name')
            )
            ->add(
                (new Filter('actions', SearchAndResetType::class))
                    ->setTypeOptions([
                        'reset_route' => 'admin_common_reset_search_by_filter_id',
                        'reset_route_params' => ['filterId' => self::GRID_ID],
                        'redirect_route' => 'admin_medbook_booking_doctor_profile_index',
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
