<?php

declare(strict_types=1);

namespace MedBook\Booking\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'medbook_clinic')]
#[ORM\Index(name: 'idx_medbook_clinic_active', columns: ['is_active'])]
#[ORM\Index(name: 'idx_medbook_clinic_city', columns: ['city'])]
final class Clinic
{
    #[ORM\Id]
    #[ORM\Column(name: 'id_clinic', type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[ORM\Column(name: 'name', type: 'string', length: 255)]
    private string $name = '';

    #[ORM\Column(name: 'address', type: 'string', length: 255)]
    private string $address = '';

    #[ORM\Column(name: 'city', type: 'string', length: 128)]
    private string $city = '';

    #[ORM\Column(name: 'postal_code', type: 'string', length: 6)]
    private string $postalCode = '';

    #[ORM\Column(name: 'voivodeship', type: 'string', length: 64, nullable: true)]
    private ?string $voivodeship = null;

    #[ORM\Column(name: 'lat', type: 'decimal', precision: 10, scale: 8, nullable: true)]
    private ?string $lat = null;

    #[ORM\Column(name: 'lng', type: 'decimal', precision: 11, scale: 8, nullable: true)]
    private ?string $lng = null;

    #[ORM\Column(name: 'phone', type: 'string', length: 32, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(name: 'email', type: 'string', length: 128, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(name: 'opening_hours_json', type: 'text', nullable: true)]
    private ?string $openingHoursJson = null;

    #[ORM\Column(name: 'is_active', type: 'boolean')]
    private bool $isActive = true;

    #[ORM\Column(name: 'date_add', type: 'datetime')]
    private \DateTimeInterface $dateAdd;

    #[ORM\Column(name: 'date_upd', type: 'datetime')]
    private \DateTimeInterface $dateUpd;

    public function __construct()
    {
        $this->dateAdd = new \DateTime();
        $this->dateUpd = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getPostalCode(): string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): self
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getVoivodeship(): ?string
    {
        return $this->voivodeship;
    }

    public function setVoivodeship(?string $voivodeship): self
    {
        $this->voivodeship = $voivodeship;

        return $this;
    }

    public function getLat(): ?string
    {
        return $this->lat;
    }

    public function setLat(?string $lat): self
    {
        $this->lat = $lat;

        return $this;
    }

    public function getLng(): ?string
    {
        return $this->lng;
    }

    public function setLng(?string $lng): self
    {
        $this->lng = $lng;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getOpeningHoursJson(): ?string
    {
        return $this->openingHoursJson;
    }

    public function setOpeningHoursJson(?string $openingHoursJson): self
    {
        $this->openingHoursJson = $openingHoursJson;

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

    public function getDateUpd(): \DateTimeInterface
    {
        return $this->dateUpd;
    }

    public function setDateUpd(\DateTimeInterface $dateUpd): self
    {
        $this->dateUpd = $dateUpd;

        return $this;
    }
}
