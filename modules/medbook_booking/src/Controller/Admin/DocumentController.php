<?php

declare(strict_types=1);

namespace MedBook\Booking\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use MedBook\Booking\Entity\Document;
use MedBook\Booking\Grid\Definition\Factory\DocumentGridDefinitionFactory;
use MedBook\Booking\Grid\Filters\DocumentFilters;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DocumentController extends PrestaShopAdminController
{
    public function indexAction(
        DocumentFilters $filters,
        #[Autowire(service: 'medbook_booking.grid.factory.documents')]
        GridFactoryInterface $gridFactory,
    ): Response {
        return $this->render('@Modules/medbook_booking/views/templates/admin/document/index.html.twig', [
            'documentGrid' => $this->presentGrid($gridFactory->getGrid($filters)),
        ]);
    }

    public function searchAction(
        Request $request,
        DocumentGridDefinitionFactory $definition,
    ): RedirectResponse {
        return $this->buildSearchResponse(
            $definition,
            $request,
            DocumentGridDefinitionFactory::GRID_ID,
            'admin_medbook_booking_document_index'
        );
    }

    public function deleteAction(int $documentId, EntityManagerInterface $entityManager): RedirectResponse
    {
        $document = $entityManager->getRepository(Document::class)->find($documentId);

        if ($document) {
            $entityManager->remove($document);
            $entityManager->flush();
            $this->addFlash('success', $this->trans('Dokument usuniety.', 'Modules.Medbookbooking.Admin'));
        } else {
            $this->addFlash('error', $this->trans('Dokument nie znaleziony.', 'Modules.Medbookbooking.Admin'));
        }

        return $this->redirectToRoute('admin_medbook_booking_document_index');
    }
}
