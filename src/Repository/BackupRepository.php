<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Backup;
use App\Entity\Database;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Backup|null find($id, $lockMode = null, $lockVersion = null)
 * @method Backup|null findOneBy(array $criteria, array $orderBy = null)
 * @method Backup[]    findAll()
 * @method Backup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BackupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Backup::class);
    }

    public function remove(Backup $backup): void
    {
        $this->getEntityManager()->remove($backup);
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    /**
     * @return Backup[]
     */
    public function getActiveBackups(Database $database): array
    {
        return $this->createQueryBuilder('b')
            ->join('b.database', 'd')
            ->andWhere('d.id = :databaseId')
            ->setParameter('databaseId', $database->getId())
            ->orderBy('b.createdAt', 'DESC')
            ->setMaxResults($database->getMaxBackups() - 1)
            ->getQuery()
            ->getResult();
    }
}
