<?php

declare(strict_types=1);

namespace App\Service;

final class BackupStatus
{
    public const STATUS_FAIL = 'fail';
    public const STATUS_OK = 'ok';

    public function __construct(
        private readonly string $status,
        private readonly ?string $errorMessage = null,
    ) {
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }
}
