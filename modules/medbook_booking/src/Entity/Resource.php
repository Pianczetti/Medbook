<?php

declare(strict_types=1);

namespace MedBook\Booking\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use MedBook\Booking\Repository\ResourceRepository;

#[ORM\Entity(repositoryClass: ResourceRepository::class)]
#[ORM\Table(name: 'medbook_resource')]
#[ORM\Index(name: 'idx_medbook_resource_active', columns: ['is_active'])]
class Resource
{
    #[ORM\Id]
    #[ORM\Column(name: 'id_resource', type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[ORM\Column(name: 'resource_type', type: 'string', length: 10)]
    private string $resourceType = 'service';

    #[ORM\Column(name: 'capacity', type: 'integer')]
    private int $capacity = 1;

    #[ORM\Column(name: 'duration_minutes', type: 'integer')]
    private int $durationMinutes = 30;

    #[ORM\Column(name: 'base_price', type: 'decimal', precision: 10, scale: 2)]
    private string $basePrice = '0.00';

    #[ORM\Column(name: 'min_duration_minutes', type: 'integer')]
    private int $minDurationMinutes = 0;

    #[ORM\Column(name: 'max_duration_minutes', type: 'integer')]
    private int $maxDurationMinutes = 0;

    #[ORM\Column(name: 'buffer_minutes', type: 'integer')]
    private int $bufferMinutes = 0;

    #[ORM\Column(name: 'is_active', type: 'boolean')]
    private bool $isActive = true;

    #[ORM\Column(name: 'id_product', type: 'integer', nullable: true)]
    private ?int $idProduct = null;

    #[ORM\Column(name: 'position', type: 'integer')]
    private int $position = 0;

    #[ORM\Column(name: 'color', type: 'string', length: 7)]
    private string $color = '#3498db';

    #[ORM\Column(name: 'date_add', type: 'datetime')]
    private \DateTimeInterface $dateAdd;

    #[ORM\Column(name: 'date_upd', type: 'datetime')]
    private \DateTimeInterface $dateUpd;

    /** @var Collection<int, ResourceLang> */
    #[ORM\OneToMany(mappedBy: 'resource', targetEntity: ResourceLang::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $resourceLangs;

    public function __construct()
    {
        $this->resourceLangs = new ArrayCollection();
        $this->dateAdd = new \DateTime();
        $this->dateUpd = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getResourceType(): string
    {
        return $this->resourceType;
    }

    public function setResourceType(string $resourceType): self
    {
        $this->resourceType = $resourceType;

        return $this;
    }

    public function getCapacity(): int
    {
        return $this->capacity;
    }

    public function setCapacity(int $capacity): self
    {
        $this->capacity = $capacity;

        return $this;
    }

    public function getDurationMinutes(): int
    {
        return $this->durationMinutes;
    }

    public function setDurationMinutes(int $durationMinutes): self
    {
        $this->durationMinutes = $durationMinutes;

        return $this;
    }

    public function getBasePrice(): string
    {
        return $this->basePrice;
    }

    public function setBasePrice(string $basePrice): self
    {
        $this->basePrice = $basePrice;

        return $this;
    }

    public function getMinDurationMinutes(): int
    {
        return $this->minDurationMinutes;
    }

    public function setMinDurationMinutes(int $minDurationMinutes): self
    {
        $this->minDurationMinutes = $minDurationMinutes;

        return $this;
    }

    public function getMaxDurationMinutes(): int
    {
        return $this->maxDurationMinutes;
    }

    public function setMaxDurationMinutes(int $maxDurationMinutes): self
    {
        $this->maxDurationMinutes = $maxDurationMinutes;

        return $this;
    }

    public function getBufferMinutes(): int
    {
        return $this->bufferMinutes;
    }

    public function setBufferMinutes(int $bufferMinutes): self
    {
        $this->bufferMinutes = $bufferMinutes;

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

    public function getIdProduct(): ?int
    {
        return $this->idProduct;
    }

    public function setIdProduct(?int $idProduct): self
    {
        $this->idProduct = $idProduct;

        return $this;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;

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

    /** @return Collection<int, ResourceLang> */
    public function getResourceLangs(): Collection
    {
        return $this->resourceLangs;
    }

    public function addResourceLang(ResourceLang $resourceLang): self
    {
        if (!$this->resourceLangs->contains($resourceLang)) {
            $this->resourceLangs->add($resourceLang);
            $resourceLang->setResource($this);
        }

        return $this;
    }

    public function removeResourceLang(ResourceLang $resourceLang): self
    {
        $this->resourceLangs->removeElement($resourceLang);

        return $this;
    }
}
