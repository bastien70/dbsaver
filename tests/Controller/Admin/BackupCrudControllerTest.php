<?php

declare(strict_types=1);

namespace App\Tests\Controller\Admin;

use App\Controller\Admin\BackupCrudController;

final class BackupCrudControllerTest extends AbstractCrudControllerTest
{
    public function testNew(): void
    {
        $url = $this->getCrudActionUrl('new');

        self::$client->request('GET', $url);
        self::assertResponseRedirects('/login');

        $this->loginAsUser();
        self::$client->request('GET', $url);
        self::assertResponseStatusCodeSame(403);
    }

    public function testEdit(): void
    {
        $url = $this->getCrudActionUrl('edit', 1);

        self::$client->request('GET', $url);
        self::assertResponseRedirects('/login');

        $this->loginAsUser();
        self::$client->request('GET', $url);
        self::assertResponseStatusCodeSame(403);
    }

    public function testDelete(): void
    {
        $url = $this->getCrudActionUrl('delete', 1);

        self::$client->request('GET', $url);
        self::assertResponseRedirects('/login');

        $this->loginAsUser();
        self::$client->request('GET', $url);
        self::assertResponseRedirects();
    }

    protected function getControllerClass(): string
    {
        return BackupCrudController::class;
    }
}
