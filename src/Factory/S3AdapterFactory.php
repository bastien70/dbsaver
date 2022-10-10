<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Enum\S3Provider;
use App\Entity\S3Adapter;
use App\Repository\S3AdapterRepository;
use Nzo\UrlEncryptorBundle\Encryptor\Encryptor;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<S3Adapter>
 *
 * @method static S3Adapter|Proxy                     createOne(array $attributes = [])
 * @method static S3Adapter[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static S3Adapter|Proxy                     find(object|array|mixed $criteria)
 * @method static S3Adapter|Proxy                     findOrCreate(array $attributes)
 * @method static S3Adapter|Proxy                     first(string $sortedField = 'id')
 * @method static S3Adapter|Proxy                     last(string $sortedField = 'id')
 * @method static S3Adapter|Proxy                     random(array $attributes = [])
 * @method static S3Adapter|Proxy                     randomOrCreate(array $attributes = [])
 * @method static S3Adapter[]|Proxy[]                 all()
 * @method static S3Adapter[]|Proxy[]                 findBy(array $attributes)
 * @method static S3Adapter[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 * @method static S3Adapter[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static S3AdapterRepository|RepositoryProxy repository()
 * @method        S3Adapter|Proxy                     create(array|callable $attributes = [])
 */
final class S3AdapterFactory extends ModelFactory
{
    public function __construct(
        private readonly Encryptor $encryptor,
    ) {
        parent::__construct();
    }

    /**
     * @return array<string, S3Provider::OTHER|string>
     */
    protected function getDefaults(): array
    {
        return [
            'name' => 'Minio',
            'prefix' => 'backups',
            's3AccessId' => 'minio',
            's3AccessSecret' => $this->encryptor->encrypt('minio123'),
            's3BucketName' => 'somebucketname',
            's3Region' => 'us-east-1',
            's3Provider' => S3Provider::OTHER,
            's3Endpoint' => 'http://127.0.0.1:9004',
        ];
    }

    protected static function getClass(): string
    {
        return S3Adapter::class;
    }
}
