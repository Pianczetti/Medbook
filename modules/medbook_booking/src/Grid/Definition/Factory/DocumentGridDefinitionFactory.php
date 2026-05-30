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
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class DocumentGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    public const GRID_ID = 'medbook_document';

    protected function getId(): string
    {
        return self::GRID_ID;
    }

    protected function getName(): string
    {
        return $this->trans('Dokumenty', [], 'Modules.Medbookbooking.Admin');
    }

    protected function getColumns(): ColumnCollection
    {
        return (new ColumnCollection())
            ->add((new BulkActionColumn('bulk'))
                ->setOptions(['bulk_field' => 'id_document'])
            )
            ->add((new DataColumn('id_document'))
                ->setName($this->trans('ID', [], 'Admin.Global'))
                ->setOptions(['field' => 'id_document'])
            )
            ->add((new DataColumn('original_name'))
                ->setName($this->trans('Nazwa pliku', [], 'Modules.Medbookbooking.Admin'))
                ->setOptions(['field' => 'original_name'])
            )
            ->add((new DataColumn('document_type'))
                ->setName($this->trans('Typ dokumentu', [], 'Modules.Medbookbooking.Admin'))
                ->setOptions(['field' => 'document_type'])
            )
            ->add((new DataColumn('id_customer'))
                ->setName($this->trans('ID klienta', [], 'Modules.Medbookbooking.Admin'))
                ->setOptions(['field' => 'id_customer'])
            )
            ->add((new DataColumn('uploaded_by'))
                ->setName($this->trans('Dodane przez', [], 'Modules.Medbookbooking.Admin'))
                ->setOptions(['field' => 'uploaded_by'])
            )
            ->add((new DataColumn('date_add'))
                ->setName($this->trans('Data dodania', [], 'Modules.Medbookbooking.Admin'))
                ->setOptions(['field' => 'date_add'])
            )
            ->add((new ActionColumn('actions'))
                ->setName($this->trans('Actions', [], 'Admin.Global'))
                ->setOptions([
                    'actions' => (new RowActionCollection())
                        ->add((new LinkRowAction('delete'))
                            ->setIcon('delete')
                            ->setOptions([
                                'route' => 'admin_medbook_booking_document_delete',
                                'route_param_name' => 'documentId',
                                'route_param_field' => 'id_document',
                            ])
                        ),
                ])
            );
    }

    protected function getFilters(): FilterCollection
    {
        return (new FilterCollection())
            ->add(
                (new Filter('id_document', TextType::class))
                    ->setTypeOptions(['required' => false])
                    ->setAssociatedColumn('id_document')
            )
            ->add(
                (new Filter('original_name', TextType::class))
                    ->setTypeOptions(['required' => false])
                    ->setAssociatedColumn('original_name')
            )
            ->add(
                (new Filter('document_type', ChoiceType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'choices' => [
                            $this->trans('Recepta', [], 'Modules.Medbookbooking.Admin') => 'prescription',
                            $this->trans('Skierowanie', [], 'Modules.Medbookbooking.Admin') => 'referral',
                            $this->trans('Wynik', [], 'Modules.Medbookbooking.Admin') => 'result',
                            $this->trans('Inne', [], 'Modules.Medbookbooking.Admin') => 'other',
                        ],
                    ])
                    ->setAssociatedColumn('document_type')
            )
            ->add(
                (new Filter('actions', SearchAndResetType::class))
                    ->setTypeOptions([
                        'reset_route' => 'admin_common_reset_search_by_filter_id',
                        'reset_route_params' => ['filterId' => self::GRID_ID],
                        'redirect_route' => 'admin_medbook_booking_document_index',
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
