<?php

declare(strict_types=1);

namespace App\Tests\Controller\Admin;

use App\Controller\Admin\S3AdapterCrudController;

class S3AdapterCrudControllerTest extends AbstractCrudControllerTest
{
    protected function getControllerClass(): string
    {
        return S3AdapterCrudController::class;
    }
}
