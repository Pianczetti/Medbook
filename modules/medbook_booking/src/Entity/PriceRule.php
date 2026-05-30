<?php

declare(strict_types=1);

namespace MedBook\Booking\Entity;

use Doctrine\ORM\Mapping as ORM;
use MedBook\Booking\Repository\PriceRuleRepository;

#[ORM\Entity(repositoryClass: PriceRuleRepository::class)]
#[ORM\Table(name: 'medbook_price_rule')]
#[ORM\Index(name: 'idx_medbook_price_rule_resource', columns: ['id_resource'])]
class PriceRule
{
    #[ORM\Id]
    #[ORM\Column(name: 'id_price_rule', type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[ORM\Column(name: 'id_resource', type: 'integer')]
    private int $idResource;

    #[ORM\Column(name: 'name', type: 'string', length: 128)]
    private string $name;

    #[ORM\Column(name: 'date_from', type: 'date', nullable: true)]
    private ?\DateTimeInterface $dateFrom = null;

    #[ORM\Column(name: 'date_to', type: 'date', nullable: true)]
    private ?\DateTimeInterface $dateTo = null;

    #[ORM\Column(name: 'day_of_week', type: 'smallint', nullable: true)]
    private ?int $dayOfWeek = null;

    #[ORM\Column(name: 'time_from', type: 'time', nullable: true)]
    private ?\DateTimeInterface $timeFrom = null;

    #[ORM\Column(name: 'time_to', type: 'time', nullable: true)]
    private ?\DateTimeInterface $timeTo = null;

    #[ORM\Column(name: 'modifier_type', type: 'string', length: 10)]
    private string $modifierType;

    #[ORM\Column(name: 'modifier_value', type: 'decimal', precision: 10, scale: 2)]
    private string $modifierValue;

    #[ORM\Column(name: 'priority', type: 'integer')]
    private int $priority = 0;

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

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDateFrom(): ?\DateTimeInterface
    {
        return $this->dateFrom;
    }

    public function setDateFrom(?\DateTimeInterface $dateFrom): self
    {
        $this->dateFrom = $dateFrom;

        return $this;
    }

    public function getDateTo(): ?\DateTimeInterface
    {
        return $this->dateTo;
    }

    public function setDateTo(?\DateTimeInterface $dateTo): self
    {
        $this->dateTo = $dateTo;

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

    public function getTimeFrom(): ?\DateTimeInterface
    {
        return $this->timeFrom;
    }

    public function setTimeFrom(?\DateTimeInterface $timeFrom): self
    {
        $this->timeFrom = $timeFrom;

        return $this;
    }

    public function getTimeTo(): ?\DateTimeInterface
    {
        return $this->timeTo;
    }

    public function setTimeTo(?\DateTimeInterface $timeTo): self
    {
        $this->timeTo = $timeTo;

        return $this;
    }

    public function getModifierType(): string
    {
        return $this->modifierType;
    }

    public function setModifierType(string $modifierType): self
    {
        $this->modifierType = $modifierType;

        return $this;
    }

    public function getModifierValue(): string
    {
        return $this->modifierValue;
    }

    public function setModifierValue(string $modifierValue): self
    {
        $this->modifierValue = $modifierValue;

        return $this;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): self
    {
        $this->priority = $priority;

        return $this;
    }

    public function isActive(): bool
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
