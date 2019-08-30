<?php

namespace App\Repository;

use App\Entity\Participant;
use App\Entity\Site;
use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use PhpParser\Node\Expr\Cast\Array_;

/**
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    /**
     * @return Sortie[] Returns an array of Sortie objects
     */

    public function findOrder()
    {
        return $this->createQueryBuilder('s')
            ->orderBy('s.site', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findBySeveralFields( Site $site, $orga, Participant $user): ?array
    {

        //$qbd = $this->createQueryBuilder('s')

        $qb = $this->createQueryBuilder('s')
             ->innerJoin('s.site', 'site', 'WITH', 'site.nom = :site')
             ->setMaxResults(10)
             ->setParameter('site', $site->getNom());

        return $qb->getQuery()->getResult();
    }

    /*
    public function findOneBySomeField($value): ?Sortie
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
