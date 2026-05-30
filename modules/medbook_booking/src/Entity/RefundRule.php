<?php

declare(strict_types=1);

namespace MedBook\Booking\Entity;

use Doctrine\ORM\Mapping as ORM;
use MedBook\Booking\Repository\RefundRuleRepository;

#[ORM\Entity(repositoryClass: RefundRuleRepository::class)]
#[ORM\Table(name: 'medbook_refund_rule')]
#[ORM\Index(name: 'idx_medbook_refund_rule_resource', columns: ['id_resource'])]
class RefundRule
{
    #[ORM\Id]
    #[ORM\Column(name: 'id_refund_rule', type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[ORM\Column(name: 'id_resource', type: 'integer', nullable: true)]
    private ?int $idResource = null;

    #[ORM\Column(name: 'hours_before', type: 'integer')]
    private int $hoursBefore;

    #[ORM\Column(name: 'refund_percent', type: 'decimal', precision: 5, scale: 2)]
    private string $refundPercent;

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

    public function getHoursBefore(): int
    {
        return $this->hoursBefore;
    }

    public function setHoursBefore(int $hoursBefore): self
    {
        $this->hoursBefore = $hoursBefore;

        return $this;
    }

    public function getRefundPercent(): string
    {
        return $this->refundPercent;
    }

    public function setRefundPercent(string $refundPercent): self
    {
        $this->refundPercent = $refundPercent;

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
