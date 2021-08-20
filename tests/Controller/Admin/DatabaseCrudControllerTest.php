<?php

declare(strict_types=1);

namespace App\Tests\Controller\Admin;

use App\Controller\Admin\DatabaseCrudController;

final class DatabaseCrudControllerTest extends AbstractCrudControllerTest
{
    protected function getControllerClass(): string
    {
        return DatabaseCrudController::class;
    }
}
