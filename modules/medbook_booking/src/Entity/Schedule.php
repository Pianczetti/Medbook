<?php

declare(strict_types=1);

namespace MedBook\Booking\Entity;

use Doctrine\ORM\Mapping as ORM;
use MedBook\Booking\Repository\ScheduleRepository;

#[ORM\Entity(repositoryClass: ScheduleRepository::class)]
#[ORM\Table(name: 'medbook_schedule')]
#[ORM\Index(name: 'idx_medbook_schedule_resource', columns: ['id_resource'])]
#[ORM\Index(name: 'idx_medbook_schedule_day', columns: ['day_of_week'])]
class Schedule
{
    #[ORM\Id]
    #[ORM\Column(name: 'id_schedule', type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[ORM\Column(name: 'id_resource', type: 'integer')]
    private int $idResource;

    #[ORM\Column(name: 'day_of_week', type: 'smallint', nullable: true)]
    private ?int $dayOfWeek = null;

    #[ORM\Column(name: 'specific_date', type: 'date', nullable: true)]
    private ?\DateTimeInterface $specificDate = null;

    #[ORM\Column(name: 'time_start', type: 'string', length: 8)]
    private string $timeStart;

    #[ORM\Column(name: 'time_end', type: 'string', length: 8)]
    private string $timeEnd;

    #[ORM\Column(name: 'is_available', type: 'boolean')]
    private bool $isAvailable = true;

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

    public function getDayOfWeek(): ?int
    {
        return $this->dayOfWeek;
    }

    public function setDayOfWeek(?int $dayOfWeek): self
    {
        $this->dayOfWeek = $dayOfWeek;

        return $this;
    }

    public function getSpecificDate(): ?\DateTimeInterface
    {
        return $this->specificDate;
    }

    public function setSpecificDate(?\DateTimeInterface $specificDate): self
    {
        $this->specificDate = $specificDate;

        return $this;
    }

    public function getTimeStart(): string
    {
        return $this->timeStart;
    }

    public function setTimeStart(string $timeStart): self
    {
        $this->timeStart = $timeStart;

        return $this;
    }

    public function getTimeEnd(): string
    {
        return $this->timeEnd;
    }

    public function setTimeEnd(string $timeEnd): self
    {
        $this->timeEnd = $timeEnd;

        return $this;
    }

    public function isAvailable(): bool
    {
        return $this->isAvailable;
    }

    public function setIsAvailable(bool $isAvailable): self
    {
        $this->isAvailable = $isAvailable;

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
