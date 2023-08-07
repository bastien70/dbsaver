<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\FtpAdapterRepository;
use App\Validator\Adapter;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[Adapter]
#[ORM\Entity(repositoryClass: FtpAdapterRepository::class)]
class FtpAdapter extends AdapterConfig
{
    #[ORM\Column(length: 255)]
    private ?string $ftpHost = null;

    #[ORM\Column(length: 255)]
    private ?string $ftpUsername = null;

    private ?string $ftpPlainPassword = null;

    #[ORM\Column(length: 255)]
    private ?string $ftpPassword = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $ftpPort = null;

    #[ORM\Column]
    private ?bool $ftpSsl = false;

    #[ORM\Column]
    private ?bool $ftpPassive = true;

    public function __toString(): string
    {
        return sprintf('FTP (%s)', $this->getName());
    }

    public function getFtpHost(): ?string
    {
        return $this->ftpHost;
    }

    public function setFtpHost(string $ftpHost): static
    {
        $this->ftpHost = $ftpHost;

        return $this;
    }

    public function getFtpUsername(): ?string
    {
        return $this->ftpUsername;
    }

    public function setFtpUsername(string $ftpUsername): static
    {
        $this->ftpUsername = $ftpUsername;

        return $this;
    }

    public function getFtpPlainPassword(): ?string
    {
        return $this->ftpPlainPassword;
    }

    public function setFtpPlainPassword(?string $ftpPlainPassword): static
    {
        $this->ftpPlainPassword = $ftpPlainPassword;

        return $this;
    }

    public function getFtpPassword(): ?string
    {
        return $this->ftpPassword;
    }

    public function setFtpPassword(string $ftpPassword): static
    {
        $this->ftpPassword = $ftpPassword;

        return $this;
    }

    public function getFtpPort(): ?int
    {
        return $this->ftpPort;
    }

    public function setFtpPort(?int $ftpPort): static
    {
        $this->ftpPort = $ftpPort;

        return $this;
    }

    public function isFtpSsl(): ?bool
    {
        return $this->ftpSsl;
    }

    public function setFtpSsl(bool $ftpSsl): static
    {
        $this->ftpSsl = $ftpSsl;

        return $this;
    }

    public function isFtpPassive(): ?bool
    {
        return $this->ftpPassive;
    }

    public function setFtpPassive(bool $ftpPassive): static
    {
        $this->ftpPassive = $ftpPassive;

        return $this;
    }
}
