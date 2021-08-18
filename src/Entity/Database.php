<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\DatabaseRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DatabaseRepository::class)]
#[ORM\Table(name: '`database`')]
class Database
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $host;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $port = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $db_user;

    private ?string $db_plain_password = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $db_password;

    #[ORM\Column(type: 'string', length: 255)]
    private string $db_name;

    #[ORM\Column(type: 'integer')]
    private int $max_backups;

    #[ORM\OneToMany(mappedBy: 'db', targetEntity: Backup::class, cascade: ['remove'], orphanRemoval: true)]
    private Collection $backups;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $createdAt;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'dbases')]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    public function __construct()
    {
        $this->backups = new ArrayCollection();
        $this->createdAt = new DateTimeImmutable();
    }

    public function __toString()
    {
        return $this->db_name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHost(): ?string
    {
        return $this->host;
    }

    public function setHost(string $host): self
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

    public function getDbUser(): ?string
    {
        return $this->db_user;
    }

    public function setDbUser(string $db_user): self
    {
        $this->db_user = $db_user;

        return $this;
    }

    public function getDbPassword(): ?string
    {
        return $this->db_password;
    }

    public function setDbPassword(string $db_password): self
    {
        $this->db_password = $db_password;

        return $this;
    }

    public function getDbName(): ?string
    {
        return $this->db_name;
    }

    public function setDbName(string $db_name): self
    {
        $this->db_name = $db_name;

        return $this;
    }

    public function getMaxBackups(): ?int
    {
        return $this->max_backups;
    }

    public function setMaxBackups(int $max_backups): self
    {
        $this->max_backups = $max_backups;

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
            $backup->setDb($this);
        }

        return $this;
    }

    public function removeBackup(Backup $backup): self
    {
        if ($this->backups->removeElement($backup)) {
            // set the owning side to null (unless already changed)
            if ($backup->getDb() === $this) {
                $backup->setDb(null);
            }
        }

        return $this;
    }

    public function getDbPlainPassword(): ?string
    {
        return $this->db_plain_password;
    }

    public function setDbPlainPassword(?string $db_plain_password): self
    {
        $this->db_plain_password = $db_plain_password;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
