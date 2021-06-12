<?php

namespace App\Repository;

use App\Entity\Stade;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Stade|null find($id, $lockMode = null, $lockVersion = null)
 * @method Stade|null findOneBy(array $criteria, array $orderBy = null)
 * @method Stade[]    findAll()
 * @method Stade[]    findAl()
 * @method Stade[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StadeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Stade::class);
    }
    // /**
    //  * @return Stade[] Returns an array of Stade objects
    //  */
    public function findAll()
    {
        return $this->findBy(array(), array('stadeRating' => 'DESC'));
    }
    public function findAl(string $search,string $city): array
    {
        $entityManager = $this->getEntityManager();
        if($city == "All"){
            $query = $entityManager->createQuery(
                'SELECT p
                FROM App\Entity\Stade p
                WHERE (p.stadeName LIKE :search OR p.stadeDescription LIKE :search)
                ORDER BY p.stadeRating DESC'
            )->setParameter('search', "%$search%");
        }else{
            $query = $entityManager->createQuery(
                'SELECT p
                FROM App\Entity\Stade p
                WHERE (p.stadeName LIKE :search OR p.stadeDescription LIKE :search) AND (p.stadeLocation LIKE :city OR p.stadeDescription LIKE :city)
                ORDER BY p.stadeRating DESC'
            )->setParameters(['search'=>"%$search%",'city'=>"%$city%"]);
        }
        

        // returns an array of Product objects
        return $query->getResult();
    }
    // /**
    //  * @return Stade[] Returns an array of Stade objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Stade
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    
}
