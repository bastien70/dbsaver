<?php

declare(strict_types=1);

namespace App\Faker\Provider;

use Faker\Generator;
use Faker\Provider\Base as BaseProvider;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

final class UserProvider extends BaseProvider
{
    public function __construct(Generator $generator, private UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct($generator);
    }

    public function encodePassword(PasswordAuthenticatedUserInterface $user, string $password): ?string
    {
        return $this->passwordHasher->hashPassword($user, $password);
    }

    public function generateEmailAddressForUser(int $iteration): string
    {
        return 'user' . $iteration . '@test.com';
    }
}
