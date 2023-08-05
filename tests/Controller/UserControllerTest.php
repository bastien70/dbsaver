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
        $this->assertRedirectsToSettings();
        $crawler = self::$client->followRedirect();
        self::assertCount(1, $crawler->filter('.alert-success'));
    }

    //    public function testEnable2fa(): void
    //    {
    //        $url = $this->adminUrlGenerator->setRoute('app_user_enable_2fa')->generateUrl();
    //
    //        self::$client->request('GET', $url);
    //        self::assertResponseRedirects('/login');
    //
    //        $this->loginAsUser();
    //        self::$client->request('GET', $url);
    //        self::assertResponseIsSuccessful();
    //    }
    //
    //    public function testDisable2fa(): void
    //    {
    //        $url = $this->adminUrlGenerator->setRoute('app_user_disable_2fa')->generateUrl();
    //
    //        self::$client->request('GET', $url);
    //        self::assertResponseRedirects('/login');
    //
    //        $this->loginAsUser();
    //        self::$client->request('GET', $url);
    //        $this->assertRedirectsToSettings();
    //        $crawler = self::$client->followRedirect();
    //        self::assertCount(1, $crawler->filter('.alert-danger'));
    //    }

    public function testInvalidateTrustedDevices(): void
    {
        $url = $this->adminUrlGenerator->setRoute('app_user_invalidate_trusted_devices')->generateUrl();

        self::$client->request('GET', $url);
        self::assertResponseRedirects('/login');

        $this->loginAsUser();
        self::$client->request('GET', $url);
        $this->assertRedirectsToSettings();
        $crawler = self::$client->followRedirect();
        self::assertCount(1, $crawler->filter('.alert-danger'));
    }

    public function testViewBackupCodes(): void
    {
        $url = $this->adminUrlGenerator->setRoute('app_user_view_backup_codes')->generateUrl();
        self::$client->request('GET', $url);
        self::assertResponseRedirects('/login');

        $this->loginAsUser();
        self::$client->request('GET', $url);
        $this->assertRedirectsToSettings();
        $crawler = self::$client->followRedirect();
        self::assertCount(1, $crawler->filter('.alert-danger'));
    }

    private function assertRedirectsToSettings(): void
    {
        $settingsUrl = $this->adminUrlGenerator->setRoute('app_user_settings')->generateUrl();
        self::assertResponseRedirects($settingsUrl);
    }
}
