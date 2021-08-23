<?php

declare(strict_types=1);

namespace App\Tests\Controller\Admin;

use App\Tests\Controller\AbstractControllerTest;

final class DashboardControllerTest extends AbstractControllerTest
{
    public function testIndex(): void
    {
        self::$client->request('GET', '/dbsaver');
        self::assertResponseRedirects('/');

        $this->loginAsUser();
        self::$client->request('GET', '/dbsaver');
        self::assertResponseIsSuccessful();
    }

    public function testSwitchLocale(): void
    {
        self::$client->request('GET', '/dbsaver/switch-locale/en');
        self::assertResponseRedirects('/');

        $this->loginAsUser();
        self::$client->request('GET', '/dbsaver/switch-locale/fr');
        self::assertResponseRedirects('/dbsaver');
        $crawler = self::$client->followRedirect();
        self::assertStringContainsString('Bienvenue', $crawler->filter('h1')->text());
        self::assertStringNotContainsString('Welcome', $crawler->filter('h1')->text());

        self::$client->request('GET', '/dbsaver/switch-locale/en');
        self::assertResponseRedirects('http://localhost/dbsaver');
        $crawler = self::$client->followRedirect();
        self::assertStringContainsString('Welcome', $crawler->filter('h1')->text());
        self::assertStringNotContainsString('Bienvenue', $crawler->filter('h1')->text());

        self::$client->request('GET', '/dbsaver/switch-locale/it');
        self::assertResponseStatusCodeSame(400);
    }
}
