<?php

namespace App\Repository;

use App\Entity\Poste;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Poste|null find($id, $lockMode = null, $lockVersion = null)
 * @method Poste|null findOneBy(array $criteria, array $orderBy = null)
 * @method Poste[]    findAll()
 * @method Poste[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PosteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Poste::class);
    }

    public function countbydate()
    {
        $query = $this->createQueryBuilder('a')
            ->select('SUBSTRING(a.date,1,7)as date_poste , count(a) as count')
            ->groupBy('date_poste');
        return $query->getQuery()->getResult();

    }
    //public function getCommentairebyid($id)
    //{
       // $conn = $this->getEntityManager()->getConnection();

       // $sql = 'SELECT * FROM poste INNER JOIN commentaire ON poste.commentaire_id = '.$id.' AND commentaire.id = '.$id.'';
        // $sql = 'SELECT * FROM destination INNER JOIN region ON destination.region_id = region.id ';

       // $stmt = $conn->prepare($sql);
       // $result =  $stmt->executeQuery(['id'=> $id]);

       // return $result->fetchAllAssociative();
   // }
    // /**
    //  * @return Poste[] Returns an array of Poste objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Poste
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function TriParLike()
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.likes','ASC ')
            ->getQuery()->getResult();
    }
    public function TriParLikeD()
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.likes','DESC ')
            ->getQuery()->getResult();
    }
}
