<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Database;
use App\Entity\Embed\BackupTask;
use App\Entity\Enum\BackupTaskPeriodicity;
use App\Entity\User;
use App\Repository\DatabaseRepository;
use Nzo\UrlEncryptorBundle\Encryptor\Encryptor;
use Zenstruck\Foundry\AnonymousFactory;
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
    public function __construct(
        private readonly Encryptor $encryptor,
    ) {
        parent::__construct();
    }

    /**
     * @param Proxy<User> $user
     */
    public function withOwner(Proxy $user): self
    {
        return $this->addState(['owner' => $user]);
    }

    public function withAdapter(Proxy $adapter): self
    {
        return $this->addState(['adapter' => $adapter]);
    }

    public function withBadPassword(): self
    {
        return $this->addState([
            'password' => $this->encryptor->encrypt('bad_password'),
            'status' => Database::STATUS_ERROR,
        ]);
    }

    /**
     * @return array<string, int|string|Proxy<object>>
     */
    protected function getDefaults(): array
    {
        return [
            'port' => '3307',
            'host' => 'localhost',
            'user' => 'root',
            'password' => $this->encryptor->encrypt('root'),
            'name' => 'dbsaver_test',
            'maxBackups' => self::faker()->numberBetween(5, 20),
            'status' => Database::STATUS_OK,
            'backupTask' => AnonymousFactory::new(BackupTask::class)->create([
                'periodicity' => BackupTaskPeriodicity::WEEK,
                'periodicityNumber' => 1,
                'startFrom' => new \DateTime('-1 day'),
                'nextIteration' => new \DateTime('-1 day'),
            ]),
        ];
    }

    protected static function getClass(): string
    {
        return Database::class;
    }
}
