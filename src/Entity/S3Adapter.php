<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Enum\S3Provider;
use App\Entity\Enum\S3StorageClass;
use App\Repository\S3AdapterRepository;
use App\Validator\Adapter;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[Adapter(groups: ['Submit'])]
#[ORM\Entity(repositoryClass: S3AdapterRepository::class)]
class S3Adapter extends AdapterConfig
{
    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Assert\NotBlank]
    private ?string $s3AccessId = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Assert\NotBlank]
    private ?string $s3AccessSecret = null;

    #[Assert\NotBlank(groups: ['Create'])]
    private ?string $s3PlainAccessSecret = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Assert\NotBlank]
    private ?string $s3BucketName = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Assert\NotBlank]
    private ?string $s3Region = null;

    #[ORM\Column(type: Types::STRING, enumType: S3Provider::class)]
    #[Assert\NotBlank]
    private ?S3Provider $s3Provider = null;

    #[ORM\Column(type: Types::STRING, enumType: S3StorageClass::class)]
    private ?S3StorageClass $storageClass = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    #[Assert\Expression(expression: "not (this.getS3Provider().value == 'other' and this.getS3Endpoint() == null)", message: 'adapter.s3.endpoint.invalid_message')]
    private ?string $s3Endpoint = null;

    public function __toString(): string
    {
        return \sprintf('S3 (%s)', $this->getName());
    }

    public function getS3AccessId(): ?string
    {
        return $this->s3AccessId;
    }

    public function setS3AccessId(string $s3AccessId): self
    {
        $this->s3AccessId = $s3AccessId;

        return $this;
    }

    public function getS3AccessSecret(): ?string
    {
        return $this->s3AccessSecret;
    }

    public function setS3AccessSecret(string $s3AccessSecret): self
    {
        $this->s3AccessSecret = $s3AccessSecret;

        return $this;
    }

    public function getS3BucketName(): ?string
    {
        return $this->s3BucketName;
    }

    public function setS3BucketName(string $s3BucketName): self
    {
        $this->s3BucketName = $s3BucketName;

        return $this;
    }

    public function getS3Region(): ?string
    {
        return $this->s3Region;
    }

    public function setS3Region(string $s3Region): self
    {
        $this->s3Region = $s3Region;

        return $this;
    }

    public function getS3Provider(): ?S3Provider
    {
        return $this->s3Provider;
    }

    public function setS3Provider(S3Provider $s3Provider): self
    {
        $this->s3Provider = $s3Provider;

        return $this;
    }

    public function getStorageClass(): ?S3StorageClass
    {
        return $this->storageClass;
    }

    public function setStorageClass(?S3StorageClass $storageClass): self
    {
        $this->storageClass = $storageClass;

        return $this;
    }

    public function getS3Endpoint(): ?string
    {
        return $this->s3Endpoint;
    }

    public function setS3Endpoint(?string $s3Endpoint): self
    {
        $this->s3Endpoint = $s3Endpoint;

        return $this;
    }

    public function getS3PlainAccessSecret(): ?string
    {
        return $this->s3PlainAccessSecret;
    }

    public function setS3PlainAccessSecret(?string $s3PlainAccessSecret): self
    {
        $this->s3PlainAccessSecret = $s3PlainAccessSecret;

        return $this;
    }
}
