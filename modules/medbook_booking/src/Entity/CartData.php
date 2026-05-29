<?php

declare(strict_types=1);

namespace MedBook\Booking\Entity;

use Doctrine\ORM\Mapping as ORM;
use MedBook\Booking\Repository\CartDataRepository;

#[ORM\Entity(repositoryClass: CartDataRepository::class)]
#[ORM\Table(name: 'medbook_cart_data')]
#[ORM\Index(name: 'idx_medbook_cart_data_cart', columns: ['id_cart'])]
#[ORM\Index(name: 'idx_medbook_cart_data_resource', columns: ['id_resource'])]
class CartData
{
    #[ORM\Id]
    #[ORM\Column(name: 'id_cart_data', type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[ORM\Column(name: 'id_cart', type: 'integer')]
    private int $idCart;

    #[ORM\Column(name: 'id_resource', type: 'integer')]
    private int $idResource;

    #[ORM\Column(name: 'booking_date', type: 'date')]
    private \DateTimeInterface $bookingDate;

    #[ORM\Column(name: 'time_start', type: 'time')]
    private \DateTimeInterface $timeStart;

    #[ORM\Column(name: 'time_end', type: 'time')]
    private \DateTimeInterface $timeEnd;

    #[ORM\Column(name: 'addons_json', type: 'text', nullable: true)]
    private ?string $addonsJson = null;

    #[ORM\Column(name: 'total_price', type: 'decimal', precision: 10, scale: 2)]
    private string $totalPrice = '0.00';

    #[ORM\Column(name: 'deposit_amount', type: 'decimal', precision: 10, scale: 2)]
    private string $depositAmount = '0.00';

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

    public function getIdCart(): int
    {
        return $this->idCart;
    }

    public function setIdCart(int $idCart): self
    {
        $this->idCart = $idCart;

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

    public function getBookingDate(): \DateTimeInterface
    {
        return $this->bookingDate;
    }

    public function setBookingDate(\DateTimeInterface $bookingDate): self
    {
        $this->bookingDate = $bookingDate;

        return $this;
    }

    public function getTimeStart(): \DateTimeInterface
    {
        return $this->timeStart;
    }

    public function setTimeStart(\DateTimeInterface $timeStart): self
    {
        $this->timeStart = $timeStart;

        return $this;
    }

    public function getTimeEnd(): \DateTimeInterface
    {
        return $this->timeEnd;
    }

    public function setTimeEnd(\DateTimeInterface $timeEnd): self
    {
        $this->timeEnd = $timeEnd;

        return $this;
    }

    public function getAddonsJson(): ?string
    {
        return $this->addonsJson;
    }

    public function setAddonsJson(?string $addonsJson): self
    {
        $this->addonsJson = $addonsJson;

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

    public function getDepositAmount(): string
    {
        return $this->depositAmount;
    }

    public function setDepositAmount(string $depositAmount): self
    {
        $this->depositAmount = $depositAmount;

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
