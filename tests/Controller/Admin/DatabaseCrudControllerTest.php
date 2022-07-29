<?php

declare(strict_types=1);

namespace App\Tests\Controller\Admin;

use App\Controller\Admin\DatabaseCrudController;

final class DatabaseCrudControllerTest extends AbstractCrudControllerTest
{
    public function testEdit(): void
    {
        $url = $this->getCrudActionUrl('edit', 1);

        self::$client->request('GET', $url);
        self::assertResponseRedirects('/login');

        $this->loginAsAdmin();
        self::$client->request('GET', $url);
        self::assertResponseStatusCodeSame(403);

        $this->loginAsUser();
        self::$client->request('GET', $url);
        self::assertResponseIsSuccessful();
    }

    public function testShowDatabaseBackupsAction(): void
    {
        $url = $this->getCrudActionUrl('showDatabaseBackupsAction', 1);

        self::$client->request('GET', $url);
        self::assertResponseRedirects('/login');

        $this->loginAsAdmin();
        self::$client->request('GET', $url);
        self::assertResponseStatusCodeSame(403);

        $this->loginAsUser();
        self::$client->request('GET', $url);
        self::assertResponseRedirects();
    }

    public function testCheckConnection(): void
    {
        $url = $this->getCrudActionUrl('checkConnection', 6);

        self::$client->request('GET', $url);
        self::assertResponseRedirects('/login');

        $this->loginAsUser();
        self::$client->request('GET', $url);
        self::assertResponseStatusCodeSame(403);

        $this->loginAsAdmin();
        self::$client->request('GET', $url);
        self::assertResponseRedirects();
        // We expect an error as parameters are randomized with fixtures.
        $crawler = self::$client->followRedirect();
        self::assertCount(1, $crawler->filter('.alert-danger'));
    }

    public function testLaunchBackupAction(): void
    {
        $url = $this->getCrudActionUrl('launchBackupAction', 6);

        self::$client->request('GET', $url);
        self::assertResponseRedirects('/login');

        $this->loginAsUser();
        self::$client->request('GET', $url);
        self::assertResponseStatusCodeSame(403);

        $this->loginAsAdmin();
        self::$client->request('GET', $url);
        self::assertResponseRedirects();
        // We expect an error as parameters are randomized with fixtures.
        $crawler = self::$client->followRedirect();
        self::assertCount(1, $crawler->filter('.alert-danger'));
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
        return DatabaseCrudController::class;
    }
}
