<?php

declare(strict_types=1);

namespace MedBook\Booking\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'medbook_addon_lang')]
class AddonLang
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Addon::class, inversedBy: 'addonLangs')]
    #[ORM\JoinColumn(name: 'id_addon', referencedColumnName: 'id_addon', nullable: false, onDelete: 'CASCADE')]
    private Addon $addon;

    #[ORM\Id]
    #[ORM\Column(name: 'id_lang', type: 'integer')]
    private int $langId;

    #[ORM\Column(name: 'name', type: 'string', length: 128)]
    private string $name;

    #[ORM\Column(name: 'description', type: 'text', nullable: true)]
    private ?string $description = null;

    public function __construct(Addon $addon, int $langId, string $name)
    {
        $this->addon = $addon;
        $this->langId = $langId;
        $this->name = $name;
    }

    public function getAddon(): Addon
    {
        return $this->addon;
    }

    public function setAddon(Addon $addon): self
    {
        $this->addon = $addon;

        return $this;
    }

    public function getLangId(): int
    {
        return $this->langId;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }
}
