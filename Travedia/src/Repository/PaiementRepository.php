<?php

namespace App\Repository;

use App\Entity\Paiement;
use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\AST\LikeExpression;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Paiement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Paiement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Paiement[]    findAll()
 * @method Paiement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaiementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Paiement::class);
    }


    public function findFacturesByUser(Utilisateur $user):array
    {
        $entityManager=$this->getEntityManager();

       // $query= $entityManager->createQuery(
       //     'SELECT paiement
       //     FROM App\Entity\Paiement paiement
       //     WHERE paiement.owner.id=user.id OR paiement.client.id=user.id
       //     '
       // )->setParameter('user',$user);

            $qb = $this->createQueryBuilder('f')
                ->join('f.client','fc')
                ->addSelect('fc')
                ->where('fc.id=:user.id')
                ->setParameter('user',$user)
                ->getQuery();



        return $qb->getResult();
    }
    // /**
    //  * @return Paiement[] Returns an array of Paiement objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Paiement
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
