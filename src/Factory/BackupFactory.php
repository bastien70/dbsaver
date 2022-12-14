<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Backup;
use App\Entity\Database;
use App\Repository\BackupRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Backup>
 *
 * @method static Backup|Proxy                     createOne(array $attributes = [])
 * @method static Backup[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static Backup|Proxy                     find(object|array|mixed $criteria)
 * @method static Backup|Proxy                     findOrCreate(array $attributes)
 * @method static Backup|Proxy                     first(string $sortedField = 'id')
 * @method static Backup|Proxy                     last(string $sortedField = 'id')
 * @method static Backup|Proxy                     random(array $attributes = [])
 * @method static Backup|Proxy                     randomOrCreate(array $attributes = [])
 * @method static Backup[]|Proxy[]                 all()
 * @method static Backup[]|Proxy[]                 findBy(array $attributes)
 * @method static Backup[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 * @method static Backup[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static BackupRepository|RepositoryProxy repository()
 * @method        Backup|Proxy                     create(array|callable $attributes = [])
 */
final class BackupFactory extends ModelFactory
{
    public function withDatabase(Database $database): self
    {
        return $this->addState(['database' => $database]);
    }

    /**
     * @return array{context: mixed, backupFile: mixed,database: Database|Proxy, backupFileName: non-empty-string, backupFileSize: 462320, mimeType: 'text/plain'}
     */
    protected function getDefaults(): array
    {
        return [
            'context' => self::faker()->randomElement([Backup::CONTEXT_AUTOMATIC, Backup::CONTEXT_MANUAL]),
            'backupFile' => self::faker()->getSqlFile(),
            'database' => DatabaseFactory::random(),
            'backupFileName' => \sprintf('backup_%s.sql', self::faker()->numberBetween(1, 9999)),
            'backupFileSize' => 462320,
            'mimeType' => 'text/plain',
        ];
    }

    protected static function getClass(): string
    {
        return Backup::class;
    }
}
