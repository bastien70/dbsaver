<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class AbstractControllerTest extends WebTestCase
{
    protected static KernelBrowser $client;

    protected function setUp(): void
    {
        static::$client = static::createClient();
    }

    protected function loginAsUser1(): void
    {
        $this->login(1);
    }

    protected function login(int $userId): void
    {
        $user = self::$client->getContainer()->get('doctrine.orm.entity_manager')
            ->getRepository(User::class)->find($userId);
        self::$client->loginUser($user);
    }
}
