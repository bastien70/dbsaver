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
            'receiveAutomaticEmails' => true,
        ]]);
        self::assertResponseRedirects();
        $crawler = self::$client->followRedirect();
        self::assertCount(1, $crawler->filter('.alert-success'));
    }

    public function testEnable2fa(): void
    {
        $url = $this->adminUrlGenerator->setRoute('app_user_enable_2fa')->generateUrl();

        self::$client->request('GET', $url);
        self::assertResponseRedirects('/login');

        $this->loginAsUser();
        self::$client->request('GET', $url);
        self::assertResponseIsSuccessful();
    }

    public function testDisable2fa(): void
    {
        $url = $this->adminUrlGenerator->setRoute('app_user_disable_2fa')->generateUrl();
        $settingsUrl = $this->adminUrlGenerator->setRoute('app_user_settings')->generateUrl();

        self::$client->request('GET', $url);
        self::assertResponseRedirects('/login');

        $this->loginAsUser();
        self::$client->request('GET', $url);
        self::assertResponseRedirects($settingsUrl);
        $crawler = self::$client->followRedirect();
        self::assertCount(1, $crawler->filter('.alert-danger'));
    }

    public function testInvalidateTrustedDevices(): void
    {
        $url = $this->adminUrlGenerator->setRoute('app_user_invalidate_trusted_devices')->generateUrl();
        $settingsUrl = $this->adminUrlGenerator->setRoute('app_user_settings')->generateUrl();

        self::$client->request('GET', $url);
        self::assertResponseRedirects('/login');

        $this->loginAsUser();
        self::$client->request('GET', $url);
        self::assertResponseRedirects($settingsUrl);
        $crawler = self::$client->followRedirect();
        self::assertCount(1, $crawler->filter('.alert-danger'));
    }
}
