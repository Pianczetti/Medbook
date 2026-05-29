<?php

declare(strict_types=1);

namespace MedBook\Booking\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'medbook_specialization')]
#[ORM\Index(name: 'idx_medbook_specialization_active', columns: ['is_active'])]
#[ORM\Index(name: 'idx_medbook_specialization_slug', columns: ['slug'])]
final class Specialization
{
    #[ORM\Id]
    #[ORM\Column(name: 'id_specialization', type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[ORM\Column(name: 'name', type: 'string', length: 128)]
    private string $name = '';

    #[ORM\Column(name: 'slug', type: 'string', length: 128)]
    private string $slug = '';

    #[ORM\Column(name: 'icon', type: 'string', length: 64, nullable: true)]
    private ?string $icon = null;

    #[ORM\Column(name: 'is_active', type: 'boolean')]
    private bool $isActive = true;

    #[ORM\Column(name: 'position', type: 'integer')]
    private int $position = 0;

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

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(?string $icon): self
    {
        $this->icon = $icon;

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
}
