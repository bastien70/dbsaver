<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface, \Stringable
{
    public const ROLE_USER = 'ROLE_USER';
    public const ROLE_ADMIN = 'ROLE_ADMIN';

    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 180, unique: true)]
    private string $email;

    #[ORM\Column(type: Types::STRING)]
    private string $role;

    /**
     * @var string The hashed password
     */
    #[ORM\Column(type: 'string')]
    private string $password;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Database::class, orphanRemoval: true)]
    private Collection $dbases;

    public function __construct()
    {
        $this->dbases = new ArrayCollection();
    }

    public function __toString(): string
    {
        return (string) $this->email;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        return [$this->role];
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection|Database[]
     */
    public function getDbases(): Collection
    {
        return $this->dbases;
    }

    public function addDbase(Database $dbase): self
    {
        if (!$this->dbases->contains($dbase)) {
            $this->dbases[] = $dbase;
            $dbase->setUser($this);
        }

        return $this;
    }

    public function removeDbase(Database $dbase): self
    {
        if ($this->dbases->removeElement($dbase)) {
            // set the owning side to null (unless already changed)
            if ($dbase->getUser() === $this) {
                $dbase->setUser(null);
            }
        }

        return $this;
    }

    public static function getAvailableRoles(): array
    {
        return [self::ROLE_USER, self::ROLE_ADMIN];
    }
}
