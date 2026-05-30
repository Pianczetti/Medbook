<?php

declare(strict_types=1);

namespace MedBook\Booking\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use MedBook\Booking\Repository\AddonRepository;

#[ORM\Entity(repositoryClass: AddonRepository::class)]
#[ORM\Table(name: 'medbook_addon')]
#[ORM\Index(name: 'idx_medbook_addon_resource', columns: ['id_resource'])]
class Addon
{
    #[ORM\Id]
    #[ORM\Column(name: 'id_addon', type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[ORM\Column(name: 'id_resource', type: 'integer', nullable: true)]
    private ?int $idResource = null;

    #[ORM\Column(name: 'price', type: 'decimal', precision: 10, scale: 2)]
    private string $price = '0.00';

    #[ORM\Column(name: 'is_active', type: 'boolean')]
    private bool $isActive = true;

    #[ORM\Column(name: 'position', type: 'integer')]
    private int $position = 0;

    #[ORM\Column(name: 'date_add', type: 'datetime')]
    private \DateTimeInterface $dateAdd;

    /** @var Collection<int, AddonLang> */
    #[ORM\OneToMany(mappedBy: 'addon', targetEntity: AddonLang::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $addonLangs;

    public function __construct()
    {
        $this->addonLangs = new ArrayCollection();
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

    public function getPrice(): string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

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

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

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

    /** @return Collection<int, AddonLang> */
    public function getAddonLangs(): Collection
    {
        return $this->addonLangs;
    }

    public function addAddonLang(AddonLang $addonLang): self
    {
        if (!$this->addonLangs->contains($addonLang)) {
            $this->addonLangs->add($addonLang);
            $addonLang->setAddon($this);
        }

        return $this;
    }

    public function removeAddonLang(AddonLang $addonLang): self
    {
        $this->addonLangs->removeElement($addonLang);

        return $this;
    }
}
