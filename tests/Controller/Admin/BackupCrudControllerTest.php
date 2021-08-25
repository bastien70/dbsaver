<?php

declare(strict_types=1);

namespace App\Tests\Controller\Admin;

use App\Controller\Admin\BackupCrudController;

final class BackupCrudControllerTest extends AbstractCrudControllerTest
{
    public function testNew(): void
    {
        $url = $this->getActionUrl('new');

        self::$client->request('GET', $url);
        self::assertResponseRedirects('/');

        $this->loginAsUser();
        self::$client->request('GET', $url);
        self::assertResponseStatusCodeSame(403);
    }

    public function testEdit(): void
    {
        $url = $this->getActionUrl('edit', 1);

        self::$client->request('GET', $url);
        self::assertResponseRedirects('/');

        $this->loginAsUser();
        self::$client->request('GET', $url);
        self::assertResponseStatusCodeSame(403);
    }

    protected function getControllerClass(): string
    {
        return BackupCrudController::class;
    }
}
