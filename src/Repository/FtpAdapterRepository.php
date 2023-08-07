<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\FtpAdapter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FtpAdapter>
 *
 * @method FtpAdapter|null find($id, $lockMode = null, $lockVersion = null)
 * @method FtpAdapter|null findOneBy(array $criteria, array $orderBy = null)
 * @method FtpAdapter[]    findAll()
 * @method FtpAdapter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class FtpAdapterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FtpAdapter::class);
    }

    public function save(FtpAdapter $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(FtpAdapter $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    //    /**
    //     * @return FtpAdapter[] Returns an array of FtpAdapter objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('f.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?FtpAdapter
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
