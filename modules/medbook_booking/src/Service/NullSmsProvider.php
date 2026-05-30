<?php

declare(strict_types=1);

namespace MedBook\Booking\Service;

/**
 * No-op SMS provider. Replace with Twilio/SMSAPI integration when ready.
 */
class NullSmsProvider implements SmsNotificationService
{
    public function send(string $phone, string $message): void
    {
        // TODO: Implement actual SMS sending (Twilio, SMSAPI, etc.)
    }
}
