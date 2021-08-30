<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\PrimaryKeyTrait;
use App\Repository\DatabaseRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DatabaseRepository::class)]
#[ORM\Table(name: '`database`')]
#[\App\Validator\Database]
class Database implements \Stringable
{
    use PrimaryKeyTrait;

    public const STATUS_UNKNOWN = 'unknown';
    public const STATUS_OK = 'ok';
    public const STATUS_ERROR = 'error';

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private ?string $host = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $port = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private ?string $user = null;

    #[Assert\NotBlank(groups: ['Create'])]
    private ?string $plainPassword = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $password = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private ?string $name = null;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotBlank]
    #[Assert\Positive]
    private int $maxBackups;

    #[ORM\OneToMany(mappedBy: 'database', targetEntity: Backup::class, orphanRemoval: true)]
    private Collection $backups;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $createdAt;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'databases')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'cascade')]
    private ?User $owner = null;

    #[ORM\Column(type: 'string', length: 10)]
    private string $status = self::STATUS_UNKNOWN;

    public function __construct()
    {
        $this->backups = new ArrayCollection();
        $this->createdAt = new DateTimeImmutable();
    }

    public function __toString(): string
    {
        return (string) $this->name;
    }

    public function getHost(): ?string
    {
        return $this->host;
    }

    public function setHost(?string $host): self
    {
        $this->host = $host;

        return $this;
    }

    public function getPort(): ?int
    {
        return $this->port;
    }

    public function setPort(?int $port): self
    {
        $this->port = $port;

        return $this;
    }

    public function getUser(): ?string
    {
        return $this->user;
    }

    public function setUser(?string $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getMaxBackups(): ?int
    {
        return $this->maxBackups;
    }

    public function setMaxBackups(int $maxBackups): self
    {
        $this->maxBackups = $maxBackups;

        return $this;
    }

    /**
     * @return Collection|Backup[]
     */
    public function getBackups(): Collection
    {
        return $this->backups;
    }

    public function addBackup(Backup $backup): self
    {
        if (!$this->backups->contains($backup)) {
            $this->backups[] = $backup;
            $backup->setDatabase($this);
        }

        return $this;
    }

    public function removeBackup(Backup $backup): self
    {
        if ($this->backups->removeElement($backup) && $backup->getDatabase() === $this) {
            $backup->setDatabase(null);
        }

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

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

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public static function getAvailableStatuses(): array
    {
        return [
            self::STATUS_UNKNOWN,
            self::STATUS_OK,
            self::STATUS_ERROR,
        ];
    }

    public function getDsn(): string
    {
        if (null === $this->port) {
            return sprintf(
                'mysql:host=%s;dbname=%s',
                $this->host,
                $this->name,
            );
        }

        return sprintf(
            'mysql:host=%s:%s;dbname=%s',
            $this->host,
            $this->port,
            $this->name,
        );
    }

    public function getDisplayDsn(): string
    {
        if (null === $this->port) {
            return sprintf(
                '%s@%s/%s',
                $this->user,
                $this->host,
                $this->name,
            );
        }

        return sprintf(
            '%s@%s:%s/%s',
            $this->user,
            $this->host,
            $this->port,
            $this->name,
        );
    }
}
