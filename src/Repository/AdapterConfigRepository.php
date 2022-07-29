<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\AdapterConfig;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AdapterConfig>
 *
 * @method AdapterConfig|null find($id, $lockMode = null, $lockVersion = null)
 * @method AdapterConfig|null findOneBy(array $criteria, array $orderBy = null)
 * @method AdapterConfig[]    findAll()
 * @method AdapterConfig[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdapterConfigRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdapterConfig::class);
    }
}
