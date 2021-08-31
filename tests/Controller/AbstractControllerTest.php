<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class AbstractControllerTest extends WebTestCase
{
    public const USER_ROLE_USER = 1;
    public const USER_ROLE_ADMIN = 2;

    protected static KernelBrowser $client;
    protected AdminUrlGenerator $adminUrlGenerator;

    protected function setUp(): void
    {
        static::$client = static::createClient();
        self::bootKernel();
        $this->adminUrlGenerator = self::getContainer()->get(AdminUrlGenerator::class);
    }

    protected function loginAsUser(): void
    {
        $this->login(self::USER_ROLE_USER);
    }

    protected function loginAsAdmin(): void
    {
        $this->login(self::USER_ROLE_ADMIN);
    }

    private function login(int $userId): void
    {
        $user = self::$client->getContainer()->get('doctrine.orm.entity_manager')
            ->getRepository(User::class)->find($userId);
        self::$client->loginUser($user);
    }
}
