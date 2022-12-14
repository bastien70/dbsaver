<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\LocalAdapterRepository;
use App\Validator\Adapter;
use Doctrine\ORM\Mapping as ORM;

#[Adapter]
#[ORM\Entity(repositoryClass: LocalAdapterRepository::class)]
class LocalAdapter extends AdapterConfig
{
    public function __toString(): string
    {
        return \sprintf('Local (%s)', $this->getName());
    }
}
