<?php

declare(strict_types=1);

namespace App\Tests\Faker\Provider;

use App\Entity\User;
use App\Faker\Provider\UserProvider;
use Faker\Generator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

final class UserProviderTest extends TestCase
{
    private UserProvider $userProvider;

    protected function setUp(): void
    {
        $passwordHasher = new class() implements UserPasswordHasherInterface {
            public function hashPassword(PasswordAuthenticatedUserInterface $user, string $plainPassword)
            {
                return null;
            }

            public function isPasswordValid(PasswordAuthenticatedUserInterface $user, string $plainPassword)
            {
                return true;
            }

            public function needsRehash(PasswordAuthenticatedUserInterface $user)
            {
                return false;
            }
        };

        $this->userProvider = new UserProvider(new Generator(), $passwordHasher);
    }

    public function testEncodePassword(): void
    {
        $user = new User();
        $password = $this->userProvider->encodePassword($user, 'password');
        self::assertNull($password); // null as there's no encoder defined
    }

    public function testGenerateEmailAddressForUser(): void
    {
        self::assertSame('user101@test.com', $this->userProvider->generateEmailAddressForUser(101));
    }
}
