<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Database;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Database|null find($id, $lockMode = null, $lockVersion = null)
 * @method Database|null findOneBy(array $criteria, array $orderBy = null)
 * @method Database[]    findAll()
 * @method Database[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DatabaseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Database::class);
    }

    public function save(Database $database): void
    {
        $this->getEntityManager()->persist($database);
        $this->getEntityManager()->flush();
    }

    /**
     * @return Database[]
     */
    public function getDatabasesToBackup(): array
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.backupTask.nextIteration <= :now')
            ->setParameter('now', new \DateTime())
            ->getQuery()
            ->getResult();
    }
}
