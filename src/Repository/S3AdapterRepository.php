<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\S3Adapter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, S3Adapter::class);
    }
}
