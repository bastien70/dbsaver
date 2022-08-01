<?php

declare(strict_types=1);

namespace App\Entity\Embed;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class Options
{
    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $resetAutoIncrement = false;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $addDropDatabase = false;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $addDropTable = false;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $addDropTrigger = true;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $addLocks = true;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $completeInsert = false;

    public function isResetAutoIncrement(): bool
    {
        return $this->resetAutoIncrement;
    }

    public function setResetAutoIncrement(bool $resetAutoIncrement): self
    {
        $this->resetAutoIncrement = $resetAutoIncrement;

        return $this;
    }

    public function isAddDropDatabase(): bool
    {
        return $this->addDropDatabase;
    }

    public function setAddDropDatabase(bool $addDropDatabase): self
    {
        $this->addDropDatabase = $addDropDatabase;

        return $this;
    }

    public function isAddDropTable(): bool
    {
        return $this->addDropTable;
    }

    public function setAddDropTable(bool $addDropTable): self
    {
        $this->addDropTable = $addDropTable;

        return $this;
    }

    public function isAddDropTrigger(): bool
    {
        return $this->addDropTrigger;
    }

    public function setAddDropTrigger(bool $addDropTrigger): self
    {
        $this->addDropTrigger = $addDropTrigger;

        return $this;
    }

    public function isAddLocks(): bool
    {
        return $this->addLocks;
    }

    public function setAddLocks(bool $addLocks): self
    {
        $this->addLocks = $addLocks;

        return $this;
    }

    public function isCompleteInsert(): bool
    {
        return $this->completeInsert;
    }

    public function setCompleteInsert(bool $completeInsert): self
    {
        $this->completeInsert = $completeInsert;

        return $this;
    }
}
