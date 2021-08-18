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

        $this->loginAsUser1();
        self::$client->request('GET', '/dbsaver');
        self::assertResponseIsSuccessful();
    }
}
