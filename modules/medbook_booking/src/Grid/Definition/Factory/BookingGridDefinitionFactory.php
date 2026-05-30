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
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DateTimeColumn;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\AbstractGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class BookingGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    public const GRID_ID = 'medbook_booking';

    protected function getId(): string
    {
        return self::GRID_ID;
    }

    protected function getName(): string
    {
        return $this->trans('Bookings', [], 'Modules.Medbookbooking.Admin');
    }

    protected function getColumns(): ColumnCollection
    {
        return (new ColumnCollection())
            ->add((new BulkActionColumn('bulk'))
                ->setOptions(['bulk_field' => 'id_booking'])
            )
            ->add((new DataColumn('id_booking'))
                ->setName($this->trans('ID', [], 'Admin.Global'))
                ->setOptions(['field' => 'id_booking'])
            )
            ->add((new DataColumn('reference_code'))
                ->setName($this->trans('Reference', [], 'Modules.Medbookbooking.Admin'))
                ->setOptions(['field' => 'reference_code'])
            )
            ->add((new DataColumn('resource_name'))
                ->setName($this->trans('Resource', [], 'Modules.Medbookbooking.Admin'))
                ->setOptions(['field' => 'resource_name'])
            )
            ->add((new DataColumn('customer_name'))
                ->setName($this->trans('Customer', [], 'Modules.Medbookbooking.Admin'))
                ->setOptions(['field' => 'customer_name'])
            )
            ->add((new DateTimeColumn('booking_date'))
                ->setName($this->trans('Date', [], 'Modules.Medbookbooking.Admin'))
                ->setOptions(['field' => 'booking_date', 'format' => 'Y-m-d'])
            )
            ->add((new DataColumn('time_start'))
                ->setName($this->trans('Start', [], 'Modules.Medbookbooking.Admin'))
                ->setOptions(['field' => 'time_start'])
            )
            ->add((new DataColumn('time_end'))
                ->setName($this->trans('End', [], 'Modules.Medbookbooking.Admin'))
                ->setOptions(['field' => 'time_end'])
            )
            ->add((new DataColumn('status'))
                ->setName($this->trans('Status', [], 'Admin.Global'))
                ->setOptions(['field' => 'status'])
            )
            ->add((new DataColumn('no_show'))
                ->setName($this->trans('No-show', [], 'Modules.Medbookbooking.Admin'))
                ->setOptions(['field' => 'no_show'])
            )
            ->add((new ActionColumn('actions'))
                ->setName($this->trans('Actions', [], 'Admin.Global'))
                ->setOptions([
                    'actions' => (new RowActionCollection())
                        ->add((new LinkRowAction('edit'))
                            ->setIcon('edit')
                            ->setOptions([
                                'route' => 'admin_medbook_booking_booking_edit',
                                'route_param_name' => 'bookingId',
                                'route_param_field' => 'id_booking',
                            ])
                        )
                        ->add((new LinkRowAction('delete'))
                            ->setIcon('delete')
                            ->setOptions([
                                'route' => 'admin_medbook_booking_booking_delete',
                                'route_param_name' => 'bookingId',
                                'route_param_field' => 'id_booking',
                            ])
                        ),
                ])
            );
    }

    protected function getFilters(): FilterCollection
    {
        return (new FilterCollection())
            ->add(
                (new Filter('id_booking', TextType::class))
                    ->setTypeOptions(['required' => false])
                    ->setAssociatedColumn('id_booking')
            )
            ->add(
                (new Filter('reference_code', TextType::class))
                    ->setTypeOptions(['required' => false])
                    ->setAssociatedColumn('reference_code')
            )
            ->add(
                (new Filter('customer_name', TextType::class))
                    ->setTypeOptions(['required' => false])
                    ->setAssociatedColumn('customer_name')
            )
            ->add(
                (new Filter('status', ChoiceType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'choices' => [
                            $this->trans('Pending', [], 'Modules.Medbookbooking.Admin') => 'pending',
                            $this->trans('Confirmed', [], 'Modules.Medbookbooking.Admin') => 'confirmed',
                            $this->trans('Cancelled', [], 'Modules.Medbookbooking.Admin') => 'cancelled',
                            $this->trans('Completed', [], 'Modules.Medbookbooking.Admin') => 'completed',
                            $this->trans('No show', [], 'Modules.Medbookbooking.Admin') => 'no_show',
                        ],
                    ])
                    ->setAssociatedColumn('status')
            )
            ->add(
                (new Filter('actions', SearchAndResetType::class))
                    ->setTypeOptions([
                        'reset_route' => 'admin_common_reset_search_by_filter_id',
                        'reset_route_params' => ['filterId' => self::GRID_ID],
                        'redirect_route' => 'admin_medbook_booking_booking_index',
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
