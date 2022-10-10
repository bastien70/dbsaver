<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\LocalAdapter;
use App\Repository\LocalAdapterRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<LocalAdapter>
 *
 * @method static LocalAdapter|Proxy                     createOne(array $attributes = [])
 * @method static LocalAdapter[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static LocalAdapter|Proxy                     find(object|array|mixed $criteria)
 * @method static LocalAdapter|Proxy                     findOrCreate(array $attributes)
 * @method static LocalAdapter|Proxy                     first(string $sortedField = 'id')
 * @method static LocalAdapter|Proxy                     last(string $sortedField = 'id')
 * @method static LocalAdapter|Proxy                     random(array $attributes = [])
 * @method static LocalAdapter|Proxy                     randomOrCreate(array $attributes = [])
 * @method static LocalAdapter[]|Proxy[]                 all()
 * @method static LocalAdapter[]|Proxy[]                 findBy(array $attributes)
 * @method static LocalAdapter[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 * @method static LocalAdapter[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static LocalAdapterRepository|RepositoryProxy repository()
 * @method        LocalAdapter|Proxy                     create(array|callable $attributes = [])
 */
final class LocalAdapterFactory extends ModelFactory
{
    /**
     * @return array<string, string|int>
     */
    protected function getDefaults(): array
    {
        return [
            'name' => 'Local',
            'prefix' => 'backups',
        ];
    }

    protected static function getClass(): string
    {
        return LocalAdapter::class;
    }
}
