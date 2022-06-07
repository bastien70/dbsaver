<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Database;
use App\Entity\User;
use App\Repository\DatabaseRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Database>
 *
 * @method static         Database|Proxy createOne(array $attributes = [])
 * @method static         Database[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static         Database|Proxy find(object|array|mixed $criteria)
 * @method static         Database|Proxy findOrCreate(array $attributes)
 * @method static         Database|Proxy first(string $sortedField = 'id')
 * @method static         Database|Proxy last(string $sortedField = 'id')
 * @method static         Database|Proxy random(array $attributes = [])
 * @method static         Database|Proxy randomOrCreate(array $attributes = [])
 * @method static         Database[]|Proxy[] all()
 * @method static         Database[]|Proxy[] findBy(array $attributes)
 * @method static         Database[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static         Database[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static         DatabaseRepository|RepositoryProxy repository()
 * @method Database|Proxy create(array|callable $attributes = [])
 */
final class DatabaseFactory extends ModelFactory
{
    /**
     * @param Proxy<User> $user
     */
    public function withOwner(Proxy $user): self
    {
        return $this->addState(['owner' => $user]);
    }

    /**
     * @return array<string, string|int>
     */
    protected function getDefaults(): array
    {
        return [
            'host' => 'localhost',
            'user' => self::faker()->userName(),
            'password' => self::faker()->word(),
            'name' => self::faker()->word(),
            'maxBackups' => self::faker()->numberBetween(5, 20),
            'status' => self::faker()->randomElement(Database::getAvailableStatuses()),
        ];
    }

    protected static function getClass(): string
    {
        return Database::class;
    }
}
