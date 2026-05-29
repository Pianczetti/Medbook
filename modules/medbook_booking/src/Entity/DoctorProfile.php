<?php

declare(strict_types=1);

namespace MedBook\Booking\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'medbook_doctor_profile')]
#[ORM\Index(name: 'idx_medbook_doctor_profile_resource', columns: ['id_resource'])]
#[ORM\Index(name: 'idx_medbook_doctor_profile_specialization', columns: ['id_specialization'])]
final class DoctorProfile
{
    #[ORM\Id]
    #[ORM\Column(name: 'id_doctor_profile', type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[ORM\Column(name: 'id_resource', type: 'integer')]
    private int $idResource = 0;

    #[ORM\Column(name: 'id_specialization', type: 'integer', nullable: true)]
    private ?int $idSpecialization = null;

    #[ORM\Column(name: 'education', type: 'text', nullable: true)]
    private ?string $education = null;

    #[ORM\Column(name: 'experience_years', type: 'integer')]
    private int $experienceYears = 0;

    #[ORM\Column(name: 'languages', type: 'string', length: 255, nullable: true)]
    private ?string $languages = null;

    #[ORM\Column(name: 'certifications', type: 'text', nullable: true)]
    private ?string $certifications = null;

    #[ORM\Column(name: 'photo', type: 'string', length: 255, nullable: true)]
    private ?string $photo = null;

    #[ORM\Column(name: 'nip', type: 'string', length: 10, nullable: true)]
    private ?string $nip = null;

    #[ORM\Column(name: 'pwz_number', type: 'string', length: 7, nullable: true)]
    private ?string $pwzNumber = null;

    #[ORM\Column(name: 'consultation_online', type: 'boolean')]
    private bool $consultationOnline = false;

    #[ORM\Column(name: 'consultation_inperson', type: 'boolean')]
    private bool $consultationInperson = true;

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

    public function getIdResource(): int
    {
        return $this->idResource;
    }

    public function setIdResource(int $idResource): self
    {
        $this->idResource = $idResource;

        return $this;
    }

    public function getIdSpecialization(): ?int
    {
        return $this->idSpecialization;
    }

    public function setIdSpecialization(?int $idSpecialization): self
    {
        $this->idSpecialization = $idSpecialization;

        return $this;
    }

    public function getEducation(): ?string
    {
        return $this->education;
    }

    public function setEducation(?string $education): self
    {
        $this->education = $education;

        return $this;
    }

    public function getExperienceYears(): int
    {
        return $this->experienceYears;
    }

    public function setExperienceYears(int $experienceYears): self
    {
        $this->experienceYears = $experienceYears;

        return $this;
    }

    public function getLanguages(): ?string
    {
        return $this->languages;
    }

    public function setLanguages(?string $languages): self
    {
        $this->languages = $languages;

        return $this;
    }

    public function getCertifications(): ?string
    {
        return $this->certifications;
    }

    public function setCertifications(?string $certifications): self
    {
        $this->certifications = $certifications;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    public function getNip(): ?string
    {
        return $this->nip;
    }

    public function setNip(?string $nip): self
    {
        $this->nip = $nip;

        return $this;
    }

    public function getPwzNumber(): ?string
    {
        return $this->pwzNumber;
    }

    public function setPwzNumber(?string $pwzNumber): self
    {
        $this->pwzNumber = $pwzNumber;

        return $this;
    }

    public function isConsultationOnline(): bool
    {
        return $this->consultationOnline;
    }

    public function setConsultationOnline(bool $consultationOnline): self
    {
        $this->consultationOnline = $consultationOnline;

        return $this;
    }

    public function isConsultationInperson(): bool
    {
        return $this->consultationInperson;
    }

    public function setConsultationInperson(bool $consultationInperson): self
    {
        $this->consultationInperson = $consultationInperson;

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
