<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\LocalAdapter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * @extends ServiceEntityRepository<LocalAdapter>
 *
 * @method LocalAdapter|null find($id, $lockMode = null, $lockVersion = null)
 * @method LocalAdapter|null findOneBy(array $criteria, array $orderBy = null)
 * @method LocalAdapter[]    findAll()
 * @method LocalAdapter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LocalAdapterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private readonly CacheInterface $cache)
    {
        parent::__construct($registry, LocalAdapter::class);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function count(array $criteria): int
    {
        return $this->cache->get('local_adapter_count', function (ItemInterface $item) use ($criteria): int {
            $item->expiresAfter(600);

            return $this->_em->getUnitOfWork()->getEntityPersister($this->_entityName)->count($criteria);
        });
    }
}
