<?php

declare(strict_types=1);

namespace MedBook\Booking\Command;

use MedBook\Booking\Service\WaitlistService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'medbook:booking:process-waitlist',
    description: 'Process waitlist: expire stale notifications and notify next matching entries',
)]
class ProcessWaitlistCommand extends Command
{
    public function __construct(
        private readonly WaitlistService $waitlistService,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $hours = (int) \Configuration::get('MEDBOOK_BOOKING_WAITLIST_NOTIFICATION_HOURS');
        if ($hours <= 0) {
            $hours = 4;
        }

        $io->info(sprintf('Expiring notifications older than %d hours...', $hours));

        $expired = $this->waitlistService->expireStaleNotifications($hours);

        $io->success(sprintf('Processed %d expired notification(s).', $expired));

        return Command::SUCCESS;
    }
}
