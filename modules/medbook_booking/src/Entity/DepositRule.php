<?php

declare(strict_types=1);

namespace MedBook\Booking\Entity;

use Doctrine\ORM\Mapping as ORM;
use MedBook\Booking\Repository\DepositRuleRepository;

#[ORM\Entity(repositoryClass: DepositRuleRepository::class)]
#[ORM\Table(name: 'medbook_deposit_rule')]
#[ORM\Index(name: 'idx_medbook_deposit_rule_resource', columns: ['id_resource'])]
class DepositRule
{
    #[ORM\Id]
    #[ORM\Column(name: 'id_deposit_rule', type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[ORM\Column(name: 'id_resource', type: 'integer', nullable: true)]
    private ?int $idResource = null;

    #[ORM\Column(name: 'deposit_type', type: 'string', length: 10)]
    private string $depositType;

    #[ORM\Column(name: 'deposit_value', type: 'decimal', precision: 10, scale: 2)]
    private string $depositValue;

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

    public function getIdResource(): ?int
    {
        return $this->idResource;
    }

    public function setIdResource(?int $idResource): self
    {
        $this->idResource = $idResource;

        return $this;
    }

    public function getDepositType(): string
    {
        return $this->depositType;
    }

    public function setDepositType(string $depositType): self
    {
        $this->depositType = $depositType;

        return $this;
    }

    public function getDepositValue(): string
    {
        return $this->depositValue;
    }

    public function setDepositValue(string $depositValue): self
    {
        $this->depositValue = $depositValue;

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
