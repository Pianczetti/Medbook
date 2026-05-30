<?php

declare(strict_types=1);

namespace MedBook\Booking\Controller\Admin;

use Doctrine\DBAL\Connection;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CalendarController extends PrestaShopAdminController
{
    public function __construct(
        private readonly Connection $connection,
        private readonly string $dbPrefix,
    ) {
    }

    public function indexAction(Request $request): Response
    {
        $languageId = (int) $this->getContext()->language->id;

        $resources = $this->connection->createQueryBuilder()
            ->select('r.id_resource', 'rl.name')
            ->from($this->dbPrefix . 'medbook_resource', 'r')
            ->innerJoin('r', $this->dbPrefix . 'medbook_resource_lang', 'rl', 'r.id_resource = rl.id_resource AND rl.id_lang = :id_lang')
            ->where('r.is_active = 1')
            ->setParameter('id_lang', $languageId)
            ->orderBy('rl.name', 'ASC')
            ->executeQuery()
            ->fetchAllAssociative();

        return $this->render('@Modules/medbook_booking/views/templates/admin/calendar/index.html.twig', [
            'resources' => $resources,
            'selectedDate' => $request->query->get('date', date('Y-m-d')),
            'selectedResourceId' => (int) $request->query->get('resource_id', '0'),
        ]);
    }

    public function dataAction(Request $request): JsonResponse
    {
        $date = $request->query->get('date', date('Y-m-d'));
        $resourceId = (int) $request->query->get('resource_id', '0');
        $languageId = (int) $this->getContext()->language->id;

        $qb = $this->connection->createQueryBuilder()
            ->select(
                'b.id_booking',
                'rl.name AS resource_name',
                'b.customer_name',
                'b.time_start',
                'b.time_end',
                'b.status'
            )
            ->from($this->dbPrefix . 'medbook_booking', 'b')
            ->leftJoin('b', $this->dbPrefix . 'medbook_resource_lang', 'rl', 'b.id_resource = rl.id_resource AND rl.id_lang = :id_lang')
            ->where('b.booking_date = :date')
            ->setParameter('id_lang', $languageId)
            ->setParameter('date', $date)
            ->orderBy('b.time_start', 'ASC');

        if ($resourceId > 0) {
            $qb->andWhere('b.id_resource = :resource_id')
                ->setParameter('resource_id', $resourceId);
        }

        $bookings = $qb->executeQuery()->fetchAllAssociative();

        $statusColors = [
            'pending' => '#ffc107',
            'confirmed' => '#28a745',
            'cancelled' => '#dc3545',
            'completed' => '#6c757d',
            'no_show' => '#17a2b8',
        ];

        $result = [];
        foreach ($bookings as $booking) {
            $result[] = [
                'id' => (int) $booking['id_booking'],
                'resource_name' => $booking['resource_name'] ?? '',
                'customer_name' => $booking['customer_name'],
                'time_start' => $booking['time_start'],
                'time_end' => $booking['time_end'],
                'status' => $booking['status'],
                'color' => $statusColors[$booking['status']] ?? '#6c757d',
            ];
        }

        return new JsonResponse($result);
    }
}
