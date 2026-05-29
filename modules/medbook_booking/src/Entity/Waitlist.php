<?php

declare(strict_types=1);

namespace MedBook\Booking\Entity;

use Doctrine\ORM\Mapping as ORM;
use MedBook\Booking\Repository\WaitlistRepository;

#[ORM\Entity(repositoryClass: WaitlistRepository::class)]
#[ORM\Table(name: 'medbook_waitlist')]
class Waitlist
{
    #[ORM\Id]
    #[ORM\Column(name: 'id_waitlist', type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[ORM\Column(name: 'id_resource', type: 'integer')]
    private int $idResource;

    #[ORM\Column(name: 'id_customer', type: 'integer', nullable: true)]
    private ?int $idCustomer = null;

    #[ORM\Column(name: 'customer_name', type: 'string', length: 128)]
    private string $customerName;

    #[ORM\Column(name: 'customer_email', type: 'string', length: 255)]
    private string $customerEmail;

    #[ORM\Column(name: 'customer_phone', type: 'string', length: 32, nullable: true)]
    private ?string $customerPhone = null;

    #[ORM\Column(name: 'preferred_dates_json', type: 'text')]
    private string $preferredDatesJson;

    #[ORM\Column(name: 'preferred_times_json', type: 'text', nullable: true)]
    private ?string $preferredTimesJson = null;

    #[ORM\Column(name: 'is_priority', type: 'boolean')]
    private bool $isPriority = false;

    #[ORM\Column(name: 'status', type: 'string', length: 16)]
    private string $status = 'active';

    #[ORM\Column(name: 'notified_at', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $notifiedAt = null;

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

    public function getIdCustomer(): ?int
    {
        return $this->idCustomer;
    }

    public function setIdCustomer(?int $idCustomer): self
    {
        $this->idCustomer = $idCustomer;

        return $this;
    }

    public function getCustomerName(): string
    {
        return $this->customerName;
    }

    public function setCustomerName(string $customerName): self
    {
        $this->customerName = $customerName;

        return $this;
    }

    public function getCustomerEmail(): string
    {
        return $this->customerEmail;
    }

    public function setCustomerEmail(string $customerEmail): self
    {
        $this->customerEmail = $customerEmail;

        return $this;
    }

    public function getCustomerPhone(): ?string
    {
        return $this->customerPhone;
    }

    public function setCustomerPhone(?string $customerPhone): self
    {
        $this->customerPhone = $customerPhone;

        return $this;
    }

    public function getPreferredDatesJson(): string
    {
        return $this->preferredDatesJson;
    }

    public function setPreferredDatesJson(string $preferredDatesJson): self
    {
        $this->preferredDatesJson = $preferredDatesJson;

        return $this;
    }

    public function getPreferredTimesJson(): ?string
    {
        return $this->preferredTimesJson;
    }

    public function setPreferredTimesJson(?string $preferredTimesJson): self
    {
        $this->preferredTimesJson = $preferredTimesJson;

        return $this;
    }

    public function getIsPriority(): bool
    {
        return $this->isPriority;
    }

    public function setIsPriority(bool $isPriority): self
    {
        $this->isPriority = $isPriority;

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

    public function getNotifiedAt(): ?\DateTimeInterface
    {
        return $this->notifiedAt;
    }

    public function setNotifiedAt(?\DateTimeInterface $notifiedAt): self
    {
        $this->notifiedAt = $notifiedAt;

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
