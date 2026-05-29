<?php

declare(strict_types=1);

namespace MedBook\Booking\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'medbook_resource_lang')]
class ResourceLang
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Resource::class, inversedBy: 'resourceLangs')]
    #[ORM\JoinColumn(name: 'id_resource', referencedColumnName: 'id_resource', nullable: false, onDelete: 'CASCADE')]
    private Resource $resource;

    #[ORM\Id]
    #[ORM\Column(name: 'id_lang', type: 'integer')]
    private int $langId;

    #[ORM\Column(name: 'name', type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(name: 'description', type: 'text', nullable: true)]
    private ?string $description = null;

    public function __construct(Resource $resource, int $langId, string $name)
    {
        $this->resource = $resource;
        $this->langId = $langId;
        $this->name = $name;
    }

    public function getResource(): Resource
    {
        return $this->resource;
    }

    public function setResource(Resource $resource): self
    {
        $this->resource = $resource;

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
