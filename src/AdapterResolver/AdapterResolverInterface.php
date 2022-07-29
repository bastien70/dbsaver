<?php

declare(strict_types=1);

namespace App\AdapterResolver;

use App\Entity\Backup;
use League\Flysystem\FilesystemAdapter;

interface AdapterResolverInterface
{
    public function getAdapter(): FilesystemAdapter;

    public function download(Backup $backup): mixed;
}
