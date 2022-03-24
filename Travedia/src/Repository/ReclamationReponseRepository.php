<?php

namespace App\Repository;

use App\Entity\ReclamationReponse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ReclamationReponse|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReclamationReponse|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReclamationReponse[]    findAll()
 * @method ReclamationReponse[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReclamationReponseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReclamationReponse::class);
    }

    // /**
    //  * @return ReclamationReponse[] Returns an array of ReclamationReponse objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ReclamationReponse
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
