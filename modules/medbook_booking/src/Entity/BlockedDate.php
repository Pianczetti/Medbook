<?php

declare(strict_types=1);

namespace MedBook\Booking\Entity;

use Doctrine\ORM\Mapping as ORM;
use MedBook\Booking\Repository\BlockedDateRepository;

#[ORM\Entity(repositoryClass: BlockedDateRepository::class)]
#[ORM\Table(name: 'medbook_blocked_date')]
#[ORM\Index(name: 'idx_medbook_blocked_resource', columns: ['id_resource', 'blocked_date'])]
class BlockedDate
{
    #[ORM\Id]
    #[ORM\Column(name: 'id_blocked_date', type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[ORM\Column(name: 'id_resource', type: 'integer', nullable: true)]
    private ?int $idResource = null;

    #[ORM\Column(name: 'blocked_date', type: 'date')]
    private \DateTimeInterface $blockedDate;

    #[ORM\Column(name: 'reason', type: 'string', length: 255, nullable: true)]
    private ?string $reason = null;

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

    public function getBlockedDate(): \DateTimeInterface
    {
        return $this->blockedDate;
    }

    public function setBlockedDate(\DateTimeInterface $blockedDate): self
    {
        $this->blockedDate = $blockedDate;

        return $this;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(?string $reason): self
    {
        $this->reason = $reason;

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
