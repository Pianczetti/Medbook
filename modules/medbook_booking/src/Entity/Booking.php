<?php

declare(strict_types=1);

namespace MedBook\Booking\Entity;

use Doctrine\ORM\Mapping as ORM;
use MedBook\Booking\Repository\BookingRepository;

#[ORM\Entity(repositoryClass: BookingRepository::class)]
#[ORM\Table(name: 'medbook_booking')]
#[ORM\Index(name: 'idx_medbook_resource_date', columns: ['id_resource', 'booking_date'])]
#[ORM\Index(name: 'idx_medbook_status', columns: ['status'])]
#[ORM\Index(name: 'idx_medbook_customer', columns: ['id_customer'])]
class Booking
{
    #[ORM\Id]
    #[ORM\Column(name: 'id_booking', type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[ORM\Column(name: 'id_resource', type: 'integer')]
    private int $idResource;

    #[ORM\Column(name: 'id_customer', type: 'integer', nullable: true)]
    private ?int $idCustomer = null;

    #[ORM\Column(name: 'id_order', type: 'integer', nullable: true)]
    private ?int $idOrder = null;

    #[ORM\Column(name: 'booking_date', type: 'date')]
    private \DateTimeInterface $bookingDate;

    #[ORM\Column(name: 'time_start', type: 'string', length: 8)]
    private string $timeStart;

    #[ORM\Column(name: 'time_end', type: 'string', length: 8)]
    private string $timeEnd;

    #[ORM\Column(name: 'status', type: 'string', length: 10)]
    private string $status = 'pending';

    #[ORM\Column(name: 'customer_name', type: 'string', length: 128)]
    private string $customerName;

    #[ORM\Column(name: 'customer_email', type: 'string', length: 128)]
    private string $customerEmail;

    #[ORM\Column(name: 'customer_phone', type: 'string', length: 32, nullable: true)]
    private ?string $customerPhone = null;

    #[ORM\Column(name: 'notes', type: 'text', nullable: true)]
    private ?string $notes = null;

    #[ORM\Column(name: 'total_price', type: 'decimal', precision: 10, scale: 2)]
    private string $totalPrice = '0.00';

    #[ORM\Column(name: 'deposit_paid', type: 'decimal', precision: 10, scale: 2)]
    private string $depositPaid = '0.00';

    #[ORM\Column(name: 'id_employee', type: 'integer', nullable: true)]
    private ?int $idEmployee = null;

    #[ORM\Column(name: 'reference_code', type: 'string', length: 16, unique: true)]
    private string $referenceCode;

    #[ORM\Column(name: 'date_add', type: 'datetime')]
    private \DateTimeInterface $dateAdd;

    #[ORM\Column(name: 'date_upd', type: 'datetime')]
    private \DateTimeInterface $dateUpd;

    #[ORM\Column(name: 'no_show', type: 'boolean')]
    private bool $noShow = false;

    #[ORM\Column(name: 'admin_notes', type: 'text', nullable: true)]
    private ?string $adminNotes = null;

    #[ORM\Column(name: 'confirmed_at', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $confirmedAt = null;

    public function __construct()
    {
        $this->dateAdd = new \DateTime();
        $this->dateUpd = new \DateTime();
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

    public function getIdOrder(): ?int
    {
        return $this->idOrder;
    }

    public function setIdOrder(?int $idOrder): self
    {
        $this->idOrder = $idOrder;

        return $this;
    }

    public function getBookingDate(): \DateTimeInterface
    {
        return $this->bookingDate;
    }

    public function setBookingDate(\DateTimeInterface $bookingDate): self
    {
        $this->bookingDate = $bookingDate;

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

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

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

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;

        return $this;
    }

    public function getTotalPrice(): string
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(string $totalPrice): self
    {
        $this->totalPrice = $totalPrice;

        return $this;
    }

    public function getDepositPaid(): string
    {
        return $this->depositPaid;
    }

    public function setDepositPaid(string $depositPaid): self
    {
        $this->depositPaid = $depositPaid;

        return $this;
    }

    public function getIdEmployee(): ?int
    {
        return $this->idEmployee;
    }

    public function setIdEmployee(?int $idEmployee): self
    {
        $this->idEmployee = $idEmployee;

        return $this;
    }

    public function getReferenceCode(): string
    {
        return $this->referenceCode;
    }

    public function setReferenceCode(string $referenceCode): self
    {
        $this->referenceCode = $referenceCode;

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

    public function getDateUpd(): \DateTimeInterface
    {
        return $this->dateUpd;
    }

    public function setDateUpd(\DateTimeInterface $dateUpd): self
    {
        $this->dateUpd = $dateUpd;

        return $this;
    }

    public function isNoShow(): bool
    {
        return $this->noShow;
    }

    public function setNoShow(bool $noShow): self
    {
        $this->noShow = $noShow;

        return $this;
    }

    public function getAdminNotes(): ?string
    {
        return $this->adminNotes;
    }

    public function setAdminNotes(?string $adminNotes): self
    {
        $this->adminNotes = $adminNotes;

        return $this;
    }

    public function getConfirmedAt(): ?\DateTimeInterface
    {
        return $this->confirmedAt;
    }

    public function setConfirmedAt(?\DateTimeInterface $confirmedAt): self
    {
        $this->confirmedAt = $confirmedAt;

        return $this;
    }
}
