<?php

declare(strict_types=1);

namespace App\Tests\Controller\Admin;

use App\Controller\Admin\FtpAdapterCrudController;

final class FtpAdapterCrudControllerTest extends AbstractCrudControllerTest
{
    protected function getControllerClass(): string
    {
        return FtpAdapterCrudController::class;
    }
}
