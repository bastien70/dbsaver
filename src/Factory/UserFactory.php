<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<User>
 *
 * @method static     User|Proxy createOne(array $attributes = [])
 * @method static     User[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static     User|Proxy find(object|array|mixed $criteria)
 * @method static     User|Proxy findOrCreate(array $attributes)
 * @method static     User|Proxy first(string $sortedField = 'id')
 * @method static     User|Proxy last(string $sortedField = 'id')
 * @method static     User|Proxy random(array $attributes = [])
 * @method static     User|Proxy randomOrCreate(array $attributes = [])
 * @method static     User[]|Proxy[] all()
 * @method static     User[]|Proxy[] findBy(array $attributes)
 * @method static     User[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static     User[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static     UserRepository|RepositoryProxy repository()
 * @method User|Proxy create(array|callable $attributes = [])
 */
final class UserFactory extends ModelFactory
{
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct();
    }

    public function asAdmin(): self
    {
        return $this->addState(['role' => User::ROLE_ADMIN]);
    }

    /**
     * @return array<string, string|bool>
     */
    protected function getDefaults(): array
    {
        return [
            'email' => self::faker()->email(),
            'role' => User::ROLE_USER,
            'locale' => 'en',
            'receiveAutomaticEmails' => self::faker()->boolean(),
            'plainPassword' => 'test',
        ];
    }

    protected function initialize(): self
    {
        return $this
            ->afterInstantiate(function (User $user): void {
                $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPlainPassword()));
            })
        ;
    }

    protected static function getClass(): string
    {
        return User::class;
    }
}
