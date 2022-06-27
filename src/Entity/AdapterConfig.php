<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\PrimaryKeyTrait;
use App\Repository\AdapterConfigRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'adapter', type: Types::STRING)]
#[ORM\DiscriminatorMap([
    'adapterConfig' => AdapterConfig::class,
    'local' => LocalAdapter::class,
    's3' => S3Adapter::class,
])]
#[ORM\Entity(repositoryClass: AdapterConfigRepository::class)]
class AdapterConfig
{
    use PrimaryKeyTrait;

    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    #[Assert\NotBlank]
    private ?string $prefix = null;

    #[ORM\OneToMany(mappedBy: 'adapter', targetEntity: Database::class, orphanRemoval: true)]
    private Collection $databases;

    public function __construct()
    {
        $this->databases = new ArrayCollection();
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPrefix(): ?string
    {
        return $this->prefix;
    }

    public function setPrefix(?string $prefix): self
    {
        $this->prefix = $prefix;

        return $this;
    }

    /**
     * @return Collection<int, Database>
     */
    public function getDatabases(): Collection
    {
        return $this->databases;
    }

    public function addDatabase(Database $database): self
    {
        if (!$this->databases->contains($database)) {
            $this->databases[] = $database;
            $database->setAdapter($this);
        }

        return $this;
    }

    public function removeDatabase(Database $database): self
    {
        if ($this->databases->removeElement($database)) {
            // set the owning side to null (unless already changed)
            if ($database->getAdapter() === $this) {
                $database->setAdapter(null);
            }
        }

        return $this;
    }

    public function getSavesCount(): int
    {
        $count = 0;

        foreach ($this->getDatabases() as $dbase) {
            $count += $dbase->getBackups()->count();
        }

        return $count;
    }
}
