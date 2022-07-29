<?php

declare(strict_types=1);

namespace App\Tests\Controller\Admin;

use App\Controller\Admin\LocalAdapterCrudController;

class LocalAdapterCrudControllerTest extends AbstractCrudControllerTest
{
    protected function getControllerClass(): string
    {
        return LocalAdapterCrudController::class;
    }
}
