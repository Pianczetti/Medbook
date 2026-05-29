<?php

declare(strict_types=1);

namespace MedBook\Booking\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'medbook_doctor_clinic')]
#[ORM\Index(name: 'idx_medbook_doctor_clinic_resource', columns: ['id_resource'])]
#[ORM\Index(name: 'idx_medbook_doctor_clinic_clinic', columns: ['id_clinic'])]
final class DoctorClinic
{
    #[ORM\Id]
    #[ORM\Column(name: 'id_doctor_clinic', type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[ORM\Column(name: 'id_resource', type: 'integer')]
    private int $idResource = 0;

    #[ORM\Column(name: 'id_clinic', type: 'integer')]
    private int $idClinic = 0;

    #[ORM\Column(name: 'room_number', type: 'string', length: 32, nullable: true)]
    private ?string $roomNumber = null;

    #[ORM\Column(name: 'is_primary', type: 'boolean')]
    private bool $isPrimary = false;

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

    public function getIdClinic(): int
    {
        return $this->idClinic;
    }

    public function setIdClinic(int $idClinic): self
    {
        $this->idClinic = $idClinic;

        return $this;
    }

    public function getRoomNumber(): ?string
    {
        return $this->roomNumber;
    }

    public function setRoomNumber(?string $roomNumber): self
    {
        $this->roomNumber = $roomNumber;

        return $this;
    }

    public function isPrimary(): bool
    {
        return $this->isPrimary;
    }

    public function setIsPrimary(bool $isPrimary): self
    {
        $this->isPrimary = $isPrimary;

        return $this;
    }
}
