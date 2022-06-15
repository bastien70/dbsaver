<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\PrimaryKeyTrait;
use App\Repository\BackupRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;

#[ORM\Entity(repositoryClass: BackupRepository::class)]
class Backup implements \Stringable
{
    use PrimaryKeyTrait;

    public const CONTEXT_MANUAL = 'manual';
    public const CONTEXT_AUTOMATIC = 'automatic';

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $backupFileName = null;

    private File $backupFile;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $mimeType = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updatedAt;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $backupFileSize = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $context;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt;

//    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
//    private ?string $originalName = null;

    #[ORM\ManyToOne(targetEntity: Database::class, inversedBy: 'backups')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'cascade')]
    private ?Database $database = null;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
    }

    public function __toString(): string
    {
        return (string) $this->database->getName() . ' - ' . $this->createdAt->format('d/m/Y H:i:s');
    }

    public function getBackupFileName(): ?string
    {
        return $this->backupFileName;
    }

    public function setBackupFileName(?string $backupFileName): self
    {
        $this->backupFileName = $backupFileName;

        return $this;
    }

    public function setBackupFile(File $backupFile = null): self
    {
        $this->backupFile = $backupFile;

        if ($backupFile) {
            $this->updatedAt = new DateTimeImmutable();
        }

        return $this;
    }

    public function getBackupFile(): ?File
    {
        return $this->backupFile;
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    public function setMimeType(?string $mimeType): self
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getBackupFileSize(): ?int
    {
        return $this->backupFileSize;
    }

    public function setBackupFileSize(?int $backupFileSize): self
    {
        $this->backupFileSize = $backupFileSize;

        return $this;
    }

    public function getContext(): ?string
    {
        return $this->context;
    }

    public function setContext(string $context): self
    {
        $this->context = $context;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /** @see \Serializable::serialize() */
    public function serialize(): ?string
    {
        return serialize([
            $this->id,
            // $this->backupFile,
        ]);
    }

    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize(string $data): void
    {
        [
            $this->id,
            // $this->backupFile,
        ] = unserialize($data, ['allowed_classes' => false]);
    }

//    public function getOriginalName(): ?string
//    {
//        return $this->originalName;
//    }
//
//    public function setOriginalName(?string $originalName): self
//    {
//        $this->originalName = $originalName;
//
//        return $this;
//    }

    public function getDatabase(): ?Database
    {
        return $this->database;
    }

    public function setDatabase(?Database $database): self
    {
        $this->database = $database;

        return $this;
    }
}
