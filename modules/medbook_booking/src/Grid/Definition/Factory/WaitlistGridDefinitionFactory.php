<?php

declare(strict_types=1);

namespace MedBook\Booking\Grid\Definition\Factory;

use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\BulkActionCollection;
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

final class WaitlistGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    public const GRID_ID = 'medbook_waitlist';

    protected function getId(): string
    {
        return self::GRID_ID;
    }

    protected function getName(): string
    {
        return $this->trans('Waitlist', [], 'Modules.Medbookbooking.Admin');
    }

    protected function getColumns(): ColumnCollection
    {
        return (new ColumnCollection())
            ->add((new BulkActionColumn('bulk'))
                ->setOptions(['bulk_field' => 'id_waitlist'])
            )
            ->add((new DataColumn('id_waitlist'))
                ->setName($this->trans('ID', [], 'Admin.Global'))
                ->setOptions(['field' => 'id_waitlist'])
            )
            ->add((new DataColumn('customer_name'))
                ->setName($this->trans('Customer', [], 'Modules.Medbookbooking.Admin'))
                ->setOptions(['field' => 'customer_name'])
            )
            ->add((new DataColumn('customer_email'))
                ->setName($this->trans('Email', [], 'Admin.Global'))
                ->setOptions(['field' => 'customer_email'])
            )
            ->add((new DataColumn('resource_name'))
                ->setName($this->trans('Resource', [], 'Modules.Medbookbooking.Admin'))
                ->setOptions(['field' => 'resource_name'])
            )
            ->add((new DataColumn('is_priority'))
                ->setName($this->trans('Priority', [], 'Modules.Medbookbooking.Admin'))
                ->setOptions(['field' => 'is_priority'])
            )
            ->add((new DataColumn('status'))
                ->setName($this->trans('Status', [], 'Admin.Global'))
                ->setOptions(['field' => 'status'])
            )
            ->add((new DateTimeColumn('date_add'))
                ->setName($this->trans('Added', [], 'Modules.Medbookbooking.Admin'))
                ->setOptions(['field' => 'date_add', 'format' => 'Y-m-d H:i'])
            )
            ->add((new ActionColumn('actions'))
                ->setName($this->trans('Actions', [], 'Admin.Global'))
                ->setOptions([
                    'actions' => (new RowActionCollection())
                        ->add((new LinkRowAction('assign'))
                            ->setIcon('check')
                            ->setName($this->trans('Assign', [], 'Modules.Medbookbooking.Admin'))
                            ->setOptions([
                                'route' => 'admin_medbook_booking_waitlist_assign',
                                'route_param_name' => 'waitlistId',
                                'route_param_field' => 'id_waitlist',
                            ])
                        )
                        ->add((new LinkRowAction('remove'))
                            ->setIcon('delete')
                            ->setName($this->trans('Remove', [], 'Modules.Medbookbooking.Admin'))
                            ->setOptions([
                                'route' => 'admin_medbook_booking_waitlist_remove',
                                'route_param_name' => 'waitlistId',
                                'route_param_field' => 'id_waitlist',
                            ])
                        ),
                ])
            );
    }

    protected function getFilters(): FilterCollection
    {
        return (new FilterCollection())
            ->add(
                (new Filter('id_waitlist', TextType::class))
                    ->setTypeOptions(['required' => false])
                    ->setAssociatedColumn('id_waitlist')
            )
            ->add(
                (new Filter('customer_name', TextType::class))
                    ->setTypeOptions(['required' => false])
                    ->setAssociatedColumn('customer_name')
            )
            ->add(
                (new Filter('customer_email', TextType::class))
                    ->setTypeOptions(['required' => false])
                    ->setAssociatedColumn('customer_email')
            )
            ->add(
                (new Filter('status', ChoiceType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'choices' => [
                            $this->trans('Active', [], 'Modules.Medbookbooking.Admin') => 'active',
                            $this->trans('Notified', [], 'Modules.Medbookbooking.Admin') => 'notified',
                            $this->trans('Booked', [], 'Modules.Medbookbooking.Admin') => 'booked',
                            $this->trans('Expired', [], 'Modules.Medbookbooking.Admin') => 'expired',
                            $this->trans('Cancelled', [], 'Modules.Medbookbooking.Admin') => 'cancelled',
                        ],
                    ])
                    ->setAssociatedColumn('status')
            )
            ->add(
                (new Filter('actions', SearchAndResetType::class))
                    ->setTypeOptions([
                        'reset_route' => 'admin_common_reset_search_by_filter_id',
                        'reset_route_params' => ['filterId' => self::GRID_ID],
                        'redirect_route' => 'admin_medbook_booking_waitlist_index',
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
        return new BulkActionCollection();
    }
}
