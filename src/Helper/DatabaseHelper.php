<?php

declare(strict_types=1);

namespace App\Helper;

use App\Entity\Database;
use Nzo\UrlEncryptorBundle\Encryptor\Encryptor;

final class DatabaseHelper
{
    private ?string $lastExceptionMessage = null;

    public function __construct(private Encryptor $encryptor)
    {
    }

    public function isConnectionOk(Database $database): bool
    {
        if (null === $database->getPort()) {
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s',
                $database->getHost(),
                $database->getName()
            );
        } else {
            $dsn = sprintf(
                'mysql:host=%s:%s;dbname=%s',
                $database->getHost(),
                $database->getPort(),
                $database->getName()
            );
        }

        try {
            $connection = new \PDO($dsn, $database->getUser(), $this->encryptor->decrypt($database->getPassword()));
            $connection = null;

            return true;
        } catch (\Exception $e) {
            $this->lastExceptionMessage = $e->getMessage();

            return false;
        }
    }

    public function getLastExceptionMessage(): ?string
    {
        return $this->lastExceptionMessage;
    }
}
