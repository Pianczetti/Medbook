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

final class RecurringGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    public const GRID_ID = 'medbook_recurring';

    protected function getId(): string
    {
        return self::GRID_ID;
    }

    protected function getName(): string
    {
        return $this->trans('Recurring Appointments', [], 'Modules.Medbookbooking.Admin');
    }

    protected function getColumns(): ColumnCollection
    {
        return (new ColumnCollection())
            ->add((new BulkActionColumn('bulk'))
                ->setOptions(['bulk_field' => 'id_suggested_visit'])
            )
            ->add((new DataColumn('id_suggested_visit'))
                ->setName($this->trans('ID', [], 'Admin.Global'))
                ->setOptions(['field' => 'id_suggested_visit'])
            )
            ->add((new DataColumn('customer_name'))
                ->setName($this->trans('Customer', [], 'Modules.Medbookbooking.Admin'))
                ->setOptions(['field' => 'customer_name'])
            )
            ->add((new DataColumn('resource_name'))
                ->setName($this->trans('Resource', [], 'Modules.Medbookbooking.Admin'))
                ->setOptions(['field' => 'resource_name'])
            )
            ->add((new DateTimeColumn('suggested_date_from'))
                ->setName($this->trans('From', [], 'Modules.Medbookbooking.Admin'))
                ->setOptions(['field' => 'suggested_date_from', 'format' => 'Y-m-d'])
            )
            ->add((new DateTimeColumn('suggested_date_to'))
                ->setName($this->trans('To', [], 'Modules.Medbookbooking.Admin'))
                ->setOptions(['field' => 'suggested_date_to', 'format' => 'Y-m-d'])
            )
            ->add((new DataColumn('status'))
                ->setName($this->trans('Status', [], 'Admin.Global'))
                ->setOptions(['field' => 'status'])
            )
            ->add((new ActionColumn('actions'))
                ->setName($this->trans('Actions', [], 'Admin.Global'))
                ->setOptions([
                    'actions' => (new RowActionCollection())
                        ->add((new LinkRowAction('dismiss'))
                            ->setIcon('close')
                            ->setName($this->trans('Dismiss', [], 'Modules.Medbookbooking.Admin'))
                            ->setOptions([
                                'route' => 'admin_medbook_booking_recurring_dismiss',
                                'route_param_name' => 'suggestedVisitId',
                                'route_param_field' => 'id_suggested_visit',
                            ])
                        ),
                ])
            );
    }

    protected function getFilters(): FilterCollection
    {
        return (new FilterCollection())
            ->add(
                (new Filter('id_suggested_visit', TextType::class))
                    ->setTypeOptions(['required' => false])
                    ->setAssociatedColumn('id_suggested_visit')
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
                            $this->trans('Booked', [], 'Modules.Medbookbooking.Admin') => 'booked',
                            $this->trans('Dismissed', [], 'Modules.Medbookbooking.Admin') => 'dismissed',
                        ],
                    ])
                    ->setAssociatedColumn('status')
            )
            ->add(
                (new Filter('actions', SearchAndResetType::class))
                    ->setTypeOptions([
                        'reset_route' => 'admin_common_reset_search_by_filter_id',
                        'reset_route_params' => ['filterId' => self::GRID_ID],
                        'redirect_route' => 'admin_medbook_booking_recurring_index',
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
