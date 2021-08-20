<?php

declare(strict_types=1);

namespace App\Tests\Controller\Admin;

use App\Controller\Admin\BackupCrudController;

final class BackupCrudControllerTest extends AbstractCrudControllerTest
{
    protected function getControllerClass(): string
    {
        return BackupCrudController::class;
    }
}
