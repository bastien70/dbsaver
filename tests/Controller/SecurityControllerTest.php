<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Bundle\SecurityBundle\DataCollector\SecurityDataCollector;

final class SecurityControllerTest extends AbstractControllerTest
{
    public function testLogin(): void
    {
        self::$client->request('GET', '/login');
        self::assertResponseIsSuccessful();

        // Unknown user
        $this->assertFailedLogin('user100@test.com', 'test');

        // Existing user, wrong password
        $this->assertFailedLogin('user@test.com', 'pass');

        // Login ok
        $this->submitLogin('user@test.com', 'test');
        self::assertResponseRedirects();
        self::assertTrue(self::getSecurityDataCollector()->isAuthenticated());

        // Already logged in: redirect
        self::$client->request('GET', '/login');
        self::assertResponseRedirects('/');
    }

    private function assertFailedLogin(string $username, string $password): void
    {
        $this->submitLogin($username, $password);
        self::assertResponseRedirects('/login');
        self::assertFalse(self::getSecurityDataCollector()->isAuthenticated());
    }

    private function submitLogin(string $email, string $password): void
    {
        $crawler = self::$client->request('GET', '/login');
        self::assertResponseIsSuccessful();
        self::$client->enableProfiler();

        $form = $crawler->selectButton('Log in')->form();
        $form['email'] = $email;
        $form['password'] = $password;
        self::$client->submit($form);
    }

    private static function getSecurityDataCollector(): SecurityDataCollector
    {
        /* @phpstan-ignore-next-line */
        return self::$client->getProfile()->getCollector('security');
    }
}
