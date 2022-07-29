<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\S3Adapter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * @extends ServiceEntityRepository<S3Adapter>
 *
 * @method S3Adapter|null find($id, $lockMode = null, $lockVersion = null)
 * @method S3Adapter|null findOneBy(array $criteria, array $orderBy = null)
 * @method S3Adapter[]    findAll()
 * @method S3Adapter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class S3AdapterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private readonly CacheInterface $cache)
    {
        parent::__construct($registry, S3Adapter::class);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function count(array $criteria): int
    {
        return $this->cache->get('s3_adapter_count', function (ItemInterface $item) use ($criteria): int {
            $item->expiresAfter(600);

            return $this->_em->getUnitOfWork()->getEntityPersister($this->_entityName)->count($criteria);
        });
    }
}
