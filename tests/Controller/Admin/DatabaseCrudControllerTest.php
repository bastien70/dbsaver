<?php

declare(strict_types=1);

namespace App\Tests\Controller\Admin;

use App\Controller\Admin\DatabaseCrudController;

final class DatabaseCrudControllerTest extends AbstractCrudControllerTest
{
    public function testEdit(): void
    {
        $url = $this->getActionUrl('edit', 1);

        self::$client->request('GET', $url);
        self::assertResponseRedirects('/');

        $this->loginAsAdmin();
        self::$client->request('GET', $url);
        self::assertResponseStatusCodeSame(403);

        $this->loginAsUser();
        self::$client->request('GET', $url);
        self::assertResponseIsSuccessful();
    }

    public function testShowDatabaseBackupsAction(): void
    {
        $url = $this->getActionUrl('showDatabaseBackupsAction', 1);

        self::$client->request('GET', $url);
        self::assertResponseRedirects('/');

        $this->loginAsAdmin();
        self::$client->request('GET', $url);
        self::assertResponseStatusCodeSame(403);

        $this->loginAsUser();
        self::$client->request('GET', $url);
        self::assertResponseRedirects();
    }

    public function testCheckConnection(): void
    {
        $url = $this->getActionUrl('checkConnection', 1);

        self::$client->request('GET', $url);
        self::assertResponseRedirects('/');

        $this->loginAsAdmin();
        self::$client->request('GET', $url);
        self::assertResponseStatusCodeSame(403);

        $this->loginAsUser();
        self::$client->request('GET', $url);
        self::assertResponseRedirects();
        // We expect an error as parameters are randomized with fixtures.
        $crawler = self::$client->followRedirect();
        self::assertCount(1, $crawler->filter('.alert-danger'));
    }

    public function testDelete(): void
    {
        $url = $this->getActionUrl('delete', 1);

        self::$client->request('GET', $url);
        self::assertResponseRedirects('/');

        $this->loginAsUser();
        self::$client->request('GET', $url);
        self::assertResponseRedirects();
    }

    protected function getControllerClass(): string
    {
        return DatabaseCrudController::class;
    }
}
