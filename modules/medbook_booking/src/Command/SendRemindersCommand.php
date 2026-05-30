<?php

declare(strict_types=1);

namespace MedBook\Booking\Command;

use MedBook\Booking\Service\ReminderService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'medbook:booking:send-reminders',
    description: 'Send booking reminder emails to customers with upcoming appointments.',
)]
final class SendRemindersCommand extends Command
{
    public function __construct(
        private readonly ReminderService $reminderService,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $hoursBefore = (int) \Configuration::get('MEDBOOK_BOOKING_REMINDER_HOURS_BEFORE');
        if ($hoursBefore <= 0) {
            $hoursBefore = 24;
        }

        $io->info(sprintf('Looking for bookings in the next %d hours...', $hoursBefore));

        $bookings = $this->reminderService->findBookingsToRemind($hoursBefore);

        if (empty($bookings)) {
            $io->success('No bookings to remind.');

            return Command::SUCCESS;
        }

        $sentCount = 0;
        $idLang = (int) \Configuration::get('PS_LANG_DEFAULT');

        foreach ($bookings as $booking) {
            $bookingId = (int) $booking['id_booking'];

            $tokens = $this->reminderService->generateTokens($bookingId);

            $confirmUrl = \Context::getContext()->link->getModuleLink(
                'medbook_booking',
                'confirm',
                ['token' => $tokens['confirm_token']],
                true
            );
            $cancelUrl = \Context::getContext()->link->getModuleLink(
                'medbook_booking',
                'confirm',
                ['token' => $tokens['cancel_token']],
                true
            );

            $templateVars = [
                '{resource_name}' => $booking['resource_name'],
                '{booking_date}' => $booking['booking_date'],
                '{booking_time}' => $booking['time_start'],
                '{customer_name}' => $booking['customer_name'],
                '{confirm_url}' => $confirmUrl,
                '{cancel_url}' => $cancelUrl,
            ];

            $sent = \Mail::send(
                $idLang,
                'booking_reminder',
                'Booking Reminder',
                $templateVars,
                $booking['customer_email'],
                $booking['customer_name'],
                null,
                null,
                null,
                null,
                _PS_MODULE_DIR_ . 'medbook_booking/mails/'
            );

            if ($sent) {
                $this->reminderService->markReminded($bookingId);
                $sentCount++;
                $io->writeln(sprintf(
                    '  Reminder sent to %s for booking #%d',
                    $booking['customer_email'],
                    $bookingId
                ));
            } else {
                $io->warning(sprintf('Failed to send reminder for booking #%d', $bookingId));
            }
        }

        $io->success(sprintf('%d reminder(s) sent.', $sentCount));

        return Command::SUCCESS;
    }
}
