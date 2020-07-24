<?php

namespace App\Repository;

use App\Entity\AccessProvider;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AccessProvider|null find($id, $lockMode = null, $lockVersion = null)
 * @method AccessProvider|null findOneBy(array $criteria, array $orderBy = null)
 * @method AccessProvider[]    findAll()
 * @method AccessProvider[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AccessProviderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AccessProvider::class);
    }

    // /**
    //  * @return AccessProvider[] Returns an array of AccessProvider objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AccessProvider
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
