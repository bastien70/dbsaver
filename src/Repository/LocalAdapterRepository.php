<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\LocalAdapter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LocalAdapter::class);
    }
}
