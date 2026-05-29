<?php

declare(strict_types=1);

namespace MedBook\Booking\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'medbook_document')]
#[ORM\Index(name: 'idx_medbook_document_customer', columns: ['id_customer'])]
#[ORM\Index(name: 'idx_medbook_document_booking', columns: ['id_booking'])]
final class Document
{
    #[ORM\Id]
    #[ORM\Column(name: 'id_document', type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[ORM\Column(name: 'id_customer', type: 'integer')]
    private int $idCustomer = 0;

    #[ORM\Column(name: 'id_booking', type: 'integer', nullable: true)]
    private ?int $idBooking = null;

    #[ORM\Column(name: 'document_type', type: 'string', length: 20)]
    private string $documentType = 'other';

    #[ORM\Column(name: 'filename', type: 'string', length: 255)]
    private string $filename = '';

    #[ORM\Column(name: 'original_name', type: 'string', length: 255)]
    private string $originalName = '';

    #[ORM\Column(name: 'mime_type', type: 'string', length: 64)]
    private string $mimeType = '';

    #[ORM\Column(name: 'file_size', type: 'integer')]
    private int $fileSize = 0;

    #[ORM\Column(name: 'uploaded_by', type: 'string', length: 10)]
    private string $uploadedBy = 'patient';

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

    public function getIdCustomer(): int
    {
        return $this->idCustomer;
    }

    public function setIdCustomer(int $idCustomer): self
    {
        $this->idCustomer = $idCustomer;

        return $this;
    }

    public function getIdBooking(): ?int
    {
        return $this->idBooking;
    }

    public function setIdBooking(?int $idBooking): self
    {
        $this->idBooking = $idBooking;

        return $this;
    }

    public function getDocumentType(): string
    {
        return $this->documentType;
    }

    public function setDocumentType(string $documentType): self
    {
        $this->documentType = $documentType;

        return $this;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    public function getOriginalName(): string
    {
        return $this->originalName;
    }

    public function setOriginalName(string $originalName): self
    {
        $this->originalName = $originalName;

        return $this;
    }

    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    public function setMimeType(string $mimeType): self
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    public function getFileSize(): int
    {
        return $this->fileSize;
    }

    public function setFileSize(int $fileSize): self
    {
        $this->fileSize = $fileSize;

        return $this;
    }

    public function getUploadedBy(): string
    {
        return $this->uploadedBy;
    }

    public function setUploadedBy(string $uploadedBy): self
    {
        $this->uploadedBy = $uploadedBy;

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
