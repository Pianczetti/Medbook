<?php

declare(strict_types=1);

namespace MedBook\Booking\Entity;

use Doctrine\ORM\Mapping as ORM;
use MedBook\Booking\Repository\SuggestedVisitRepository;

#[ORM\Entity(repositoryClass: SuggestedVisitRepository::class)]
#[ORM\Table(name: 'medbook_suggested_visit')]
class SuggestedVisit
{
    #[ORM\Id]
    #[ORM\Column(name: 'id_suggested_visit', type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[ORM\Column(name: 'id_booking', type: 'integer')]
    private int $idBooking;

    #[ORM\Column(name: 'id_resource', type: 'integer')]
    private int $idResource;

    #[ORM\Column(name: 'id_customer', type: 'integer')]
    private int $idCustomer;

    #[ORM\Column(name: 'suggested_date_from', type: 'date')]
    private \DateTimeInterface $suggestedDateFrom;

    #[ORM\Column(name: 'suggested_date_to', type: 'date')]
    private \DateTimeInterface $suggestedDateTo;

    #[ORM\Column(name: 'status', type: 'string', length: 16)]
    private string $status = 'pending';

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

    public function getIdBooking(): int
    {
        return $this->idBooking;
    }

    public function setIdBooking(int $idBooking): self
    {
        $this->idBooking = $idBooking;

        return $this;
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

    public function getIdCustomer(): int
    {
        return $this->idCustomer;
    }

    public function setIdCustomer(int $idCustomer): self
    {
        $this->idCustomer = $idCustomer;

        return $this;
    }

    public function getSuggestedDateFrom(): \DateTimeInterface
    {
        return $this->suggestedDateFrom;
    }

    public function setSuggestedDateFrom(\DateTimeInterface $suggestedDateFrom): self
    {
        $this->suggestedDateFrom = $suggestedDateFrom;

        return $this;
    }

    public function getSuggestedDateTo(): \DateTimeInterface
    {
        return $this->suggestedDateTo;
    }

    public function setSuggestedDateTo(\DateTimeInterface $suggestedDateTo): self
    {
        $this->suggestedDateTo = $suggestedDateTo;

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
