<?php

declare(strict_types=1);

namespace MedBook\Booking\Service;

use Doctrine\ORM\EntityManagerInterface;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use MedBook\Booking\Entity\Booking;

class BookingStatusService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly HookDispatcherInterface $hookDispatcher,
    ) {
    }

    public function changeStatus(int $bookingId, string $newStatus): void
    {
        $booking = $this->entityManager->getRepository(Booking::class)->find($bookingId);

        if (!$booking) {
            throw new \RuntimeException(sprintf('Booking #%d not found.', $bookingId));
        }

        $booking->setStatus($newStatus);
        $booking->setDateUpd(new \DateTime());

        $this->entityManager->flush();

        if ('cancelled' === $newStatus) {
            $this->hookDispatcher->dispatchWithParameters('actionBookingCancelled', [
                'id_booking' => $bookingId,
                'id_resource' => $booking->getIdResource(),
            ]);
        }

        if ('completed' === $newStatus) {
            $this->hookDispatcher->dispatchWithParameters('actionBookingCompleted', [
                'id_booking' => $bookingId,
                'id_resource' => $booking->getIdResource(),
            ]);
        }
    }
}
