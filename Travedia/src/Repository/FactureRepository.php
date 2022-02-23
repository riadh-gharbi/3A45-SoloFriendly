<?php

namespace App\Repository;

use App\Entity\Facture;
use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\AST\LikeExpression;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Facture|null find($id, $lockMode = null, $lockVersion = null)
 * @method Facture|null findOneBy(array $criteria, array $orderBy = null)
 * @method Facture[]    findAll()
 * @method Facture[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FactureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Facture::class);
    }


    public function findFacturesByUser(Utilisateur $user):array
    {
        $entityManager=$this->getEntityManager();

       // $query= $entityManager->createQuery(
       //     'SELECT facture
       //     FROM App\Entity\Facture facture
       //     WHERE facture.owner.id=user.id OR facture.client.id=user.id
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
    //  * @return Facture[] Returns an array of Facture objects
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
    public function findOneBySomeField($value): ?Facture
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
