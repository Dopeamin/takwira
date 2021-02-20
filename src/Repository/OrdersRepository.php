<?php

namespace App\Repository;

use App\Entity\Stade;
use App\Entity\Orders;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Orders|null find($id, $lockMode = null, $lockVersion = null)
 * @method Orders|null findOneBy(array $criteria, array $orderBy = null)
 * @method Orders[]    findAll()
 * @method Orders[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrdersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Orders::class);
    }

    // /**
    //  * @return Orders[] Returns an array of Orders objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Orders
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function getByDate(\Datetime $startDate,\Datetime $endDate,Stade $stade)
    {

        $qb = $this->createQueryBuilder("e");
        $qb
            ->andWhere('e.startDate < :to AND e.endDate > :from AND e.verified = 1 AND e.Stade = :stade ')
            ->setParameter('from', $startDate )
            ->setParameter('to', $endDate)
            ->setParameter('stade', $stade)
        ;
        $result = $qb->getQuery()->getResult();

        return $result;
    }
}
