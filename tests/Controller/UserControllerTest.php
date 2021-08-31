<?php

declare(strict_types=1);

namespace App\Tests\Controller;

final class UserControllerTest extends AbstractControllerTest
{
    public function testSettings(): void
    {
        $url = $this->adminUrlGenerator->setRoute('app_user_settings')->generateUrl();

        self::$client->request('GET', $url);
        self::assertResponseRedirects('/login');

        $this->loginAsUser();
        self::$client->request('GET', $url);
        self::assertResponseIsSuccessful();

        self::$client->submitForm('_submit', ['settings' => [
            'locale' => 'en',
            'currentPassword' => 'test',
            'newPassword' => 'test',
        ]]);
        self::assertResponseRedirects();
        $crawler = self::$client->followRedirect();
        self::assertCount(1, $crawler->filter('.alert-success'));
    }
}
