<?php

declare(strict_types=1);

namespace App\Entity\Embed;

use App\Entity\Enum\BackupTaskPeriodicity;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Embeddable;
use Symfony\Component\Validator\Constraints as Assert;

#[Embeddable]
class BackupTask
{
    #[ORM\Column(type: Types::STRING, length: 255, enumType: BackupTaskPeriodicity::class)]
    #[Assert\NotBlank]
    private ?BackupTaskPeriodicity $periodicity = null;

    #[ORM\Column(type: Types::INTEGER)]
    #[Assert\Type(type: 'integer')]
    #[Assert\Range(min: 1)]
    #[Assert\NotBlank]
    private ?int $periodicityNumber = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\GreaterThanOrEqual(value: 'tomorrow')]
    #[Assert\NotBlank]
    private ?\DateTimeInterface $startFrom = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $nextIteration = null;

    public function getPeriodicity(): ?BackupTaskPeriodicity
    {
        return $this->periodicity;
    }

    public function setPeriodicity(BackupTaskPeriodicity $periodicity): self
    {
        $this->periodicity = $periodicity;

        return $this;
    }

    public function getPeriodicityNumber(): ?int
    {
        return $this->periodicityNumber;
    }

    public function setPeriodicityNumber(int $periodicityNumber): self
    {
        $this->periodicityNumber = $periodicityNumber;

        return $this;
    }

    public function getStartFrom(): ?\DateTimeInterface
    {
        return $this->startFrom;
    }

    public function setStartFrom(\DateTimeInterface $startFrom): self
    {
        $this->startFrom = $startFrom;

        return $this;
    }

    public function getNextIteration(): ?\DateTimeInterface
    {
        return $this->nextIteration;
    }

    public function setNextIteration(?\DateTimeInterface $nextIteration): self
    {
        $this->nextIteration = $nextIteration;

        return $this;
    }

    public function calculateNextIteration(): \DateTime|bool
    {
        $currentIteration = new \DateTime();
        $currentIteration->setTime(0, 0);

        return $currentIteration->modify(
            $this->getNextIterationStringAdd(
                $this->getPeriodicityNumber(),
                $this->getPeriodicity()->value
            )
        );
    }

    public function getDescriptionPrefixTranslation(): string
    {
        if (1 === $this->periodicityNumber) {
            return 'enum.backup_task_periodicity.prefix.singular';
        }

        return match ($this->periodicity) {
            BackupTaskPeriodicity::DAY, BackupTaskPeriodicity::MONTH => 'enum.backup_task_periodicity.prefix.masculine_plural',
            BackupTaskPeriodicity::WEEK, BackupTaskPeriodicity::YEAR => 'enum.backup_task_periodicity.prefix.feminine_plural',
            null => throw new \LogicException('Periodicity is not set'),
        };
    }

    public function getDescriptionSuffixTranslation(): string
    {
        if (1 === $this->periodicityNumber) {
            return \sprintf('enum.backup_task_periodicity.suffix.singular.%s', $this->periodicity->value);
        }

        return \sprintf('enum.backup_task_periodicity.suffix.plural.%s', $this->periodicity->value);
    }

    private function getNextIterationStringAdd(float $value, string $string): string
    {
        return \sprintf('+ %s %s', $value, $string);
    }
}
