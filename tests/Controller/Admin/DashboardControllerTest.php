<?php

declare(strict_types=1);

namespace App\Tests\Controller\Admin;

use App\Tests\Controller\AbstractControllerTest;

final class DashboardControllerTest extends AbstractControllerTest
{
    public function testIndex(): void
    {
        self::$client->request('GET', '/');
        self::assertResponseRedirects('/login');

        $this->loginAsUser();
        self::$client->request('GET', '/');
        self::assertResponseIsSuccessful();
    }

    public function testSwitchLocale(): void
    {
        self::$client->request('GET', '/?routeName=admin_switch_locale&routeParams[locale]=en');
        self::assertResponseRedirects('/login');

        $this->loginAsUser();
        self::$client->request('GET', '/?routeName=admin_switch_locale&routeParams[locale]=fr');
        self::assertResponseRedirects('/');
        $crawler = self::$client->followRedirect();
        self::assertStringContainsString('Bienvenue', $crawler->filter('h1')->text());
        self::assertStringNotContainsString('Welcome', $crawler->filter('h1')->text());

        self::$client->request('GET', '/?routeName=admin_switch_locale&routeParams[locale]=en');
        self::assertResponseRedirects('http://localhost/');
        $crawler = self::$client->followRedirect();
        self::assertStringContainsString('Welcome', $crawler->filter('h1')->text());
        self::assertStringNotContainsString('Bienvenue', $crawler->filter('h1')->text());

        self::$client->request('GET', '/?routeName=admin_switch_locale&routeParams[locale]=it');
        self::assertResponseStatusCodeSame(400);
    }
}
