<?php

declare(strict_types=1);

namespace MedBook\Booking\Entity;

use Doctrine\ORM\Mapping as ORM;
use MedBook\Booking\Repository\RecurrenceConfigRepository;

#[ORM\Entity(repositoryClass: RecurrenceConfigRepository::class)]
#[ORM\Table(name: 'medbook_recurrence_config')]
class RecurrenceConfig
{
    #[ORM\Id]
    #[ORM\Column(name: 'id_recurrence_config', type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[ORM\Column(name: 'id_resource', type: 'integer')]
    private int $idResource;

    #[ORM\Column(name: 'interval_days', type: 'integer')]
    private int $intervalDays = 30;

    #[ORM\Column(name: 'tolerance_days', type: 'integer')]
    private int $toleranceDays = 3;

    #[ORM\Column(name: 'is_active', type: 'boolean')]
    private bool $isActive = true;

    #[ORM\Column(name: 'date_add', type: 'datetime')]
    private \DateTimeInterface $dateAdd;

    public function __construct()
    {
        $this->dateAdd = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdResource(): int
    {
        return $this->idResource;
    }

    public function setIdResource(int $idResource): self
    {
        $this->idResource = $idResource;

        return $this;
    }

    public function getIntervalDays(): int
    {
        return $this->intervalDays;
    }

    public function setIntervalDays(int $intervalDays): self
    {
        $this->intervalDays = $intervalDays;

        return $this;
    }

    public function getToleranceDays(): int
    {
        return $this->toleranceDays;
    }

    public function setToleranceDays(int $toleranceDays): self
    {
        $this->toleranceDays = $toleranceDays;

        return $this;
    }

    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getDateAdd(): \DateTimeInterface
    {
        return $this->dateAdd;
    }

    public function setDateAdd(\DateTimeInterface $dateAdd): self
    {
        $this->dateAdd = $dateAdd;

        return $this;
    }
}
