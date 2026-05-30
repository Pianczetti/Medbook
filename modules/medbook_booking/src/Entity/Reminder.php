<?php

declare(strict_types=1);

namespace MedBook\Booking\Entity;

use Doctrine\ORM\Mapping as ORM;
use MedBook\Booking\Repository\ReminderRepository;

#[ORM\Entity(repositoryClass: ReminderRepository::class)]
#[ORM\Table(name: 'medbook_reminder')]
class Reminder
{
    #[ORM\Id]
    #[ORM\Column(name: 'id_reminder', type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[ORM\Column(name: 'id_booking', type: 'integer')]
    private int $idBooking;

    #[ORM\Column(name: 'reminder_type', type: 'string', length: 32)]
    private string $reminderType = 'email';

    #[ORM\Column(name: 'sent_at', type: 'datetime')]
    private \DateTimeInterface $sentAt;

    #[ORM\Column(name: 'status', type: 'string', length: 16)]
    private string $status = 'sent';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdBooking(): int
    {
        return $this->idBooking;
    }

    public function setIdBooking(int $idBooking): self
    {
        $this->idBooking = $idBooking;

        return $this;
    }

    public function getReminderType(): string
    {
        return $this->reminderType;
    }

    public function setReminderType(string $reminderType): self
    {
        $this->reminderType = $reminderType;

        return $this;
    }

    public function getSentAt(): \DateTimeInterface
    {
        return $this->sentAt;
    }

    public function setSentAt(\DateTimeInterface $sentAt): self
    {
        $this->sentAt = $sentAt;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }
}
