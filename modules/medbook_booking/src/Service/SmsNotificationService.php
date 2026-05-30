<?php

declare(strict_types=1);

namespace MedBook\Booking\Service;

interface SmsNotificationService
{
    /**
     * Send an SMS message to the given phone number.
     */
    public function send(string $phone, string $message): void;
}
