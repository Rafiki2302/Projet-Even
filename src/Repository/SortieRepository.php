<?php

namespace App\Repository;

use App\Entity\Participant;
use App\Entity\Site;
use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\AST\Functions\CurrentDateFunction;
use Doctrine\ORM\QueryBuilder;
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

    public function findOrga( Site $site, $date1, $date2, Participant $user, $nom): ?array
    {
        $qb = $this->createQueryBuilder('s')
            ->innerJoin('s.site', 'site', 'WITH', 'site.nom = :site')
            ->setMaxResults(10)
            ->setParameter('site', $site->getNom())
            ->andWhere('s.datecloture > :dateJ')
            ->setParameter('dateJ', new \DateTime('now'))
            ->andWhere('s.datedebut>= :date1')
            ->setParameter('date1', $date1)
            ->andWhere('s.datedebut<= :date2')
            ->setParameter('date2', $date2)
            ->innerJoin('s.organisateur', 'org', 'WITH', 'org.id = :user')
            ->setParameter('user', $user->getId());
                if ($nom){
                    $qb->andWhere('s.nom LIKE :nom')
                        ->setParameter('nom', '%'.$nom.'%');
        };
			 return $qb->getQuery()->getResult();
	}

    public function findInsc( Site $site, $date1, $date2, Participant $user, $nom): ?array
    {
        $qb = $this->createQueryBuilder('s')
            ->innerJoin('s.site', 'site', 'WITH', 'site.nom = :site')
            ->setMaxResults(10)
            ->setParameter('site', $site->getNom())
            ->andWhere('s.datecloture > :dateJ')
            ->setParameter('dateJ', new \DateTime('now'))
            ->andWhere('s.datedebut>= :date1')
            ->setParameter('date1', $date1)
            ->andWhere('s.datedebut<= :date2')
            ->setParameter('date2', $date2)
			 ->innerJoin('s.participants', 'part', 'WITH', 'part.id = :user')
            ->setParameter('user', $user->getId());
                if ($nom){
                    $qb->andWhere('s.nom LIKE :nom')
                    ->setParameter('nom', '%'.$nom.'%');
            }
			 return $qb->getQuery()->getResult();
	}

    public function findPass( Site $site, $date1, $date2, Participant $user, $nom): ?array
    {
        $qb = $this->createQueryBuilder('s')
            ->innerJoin('s.site', 'site', 'WITH', 'site.nom = :site')
            ->setMaxResults(10)
            ->setParameter('site', $site->getNom())
            ->andWhere('s.datedebut < :dateJ')
            ->setParameter('dateJ', new \DateTime('now'))
            ->andWhere('s.datedebut>= :date1')
            ->setParameter('date1', $date1)
            ->andWhere('s.datedebut<= :date2')
            ->setParameter('date2', $date2);
                if ($nom){
                    $qb->andWhere('s.nom LIKE :nom')
                        ->setParameter('nom', '%'.$nom.'%');
            }
			 return $qb->getQuery()->getResult();
	}

    public function findBySeveralFields( Site $site, Participant $user, $nom, $date1, $date2): ?array
    {
        //$qbd = $this->createQueryBuilder('s')

        $qb = $this->createQueryBuilder('s')
            ->innerJoin('s.site', 'site', 'WITH', 'site.nom = :site')
            ->setMaxResults(10)
            ->setParameter('site', $site->getNom())
            ->andWhere('s.datecloture > :dateJ')
            ->setParameter('dateJ', new \DateTime('now'))
            ->andWhere('s.datedebut>= :date1')
            ->setParameter('date1', $date1)
            ->andWhere('s.datedebut<= :date2')
            ->setParameter('date2', $date2);

        if ($nom){
            $qb->andWhere('s.nom LIKE :nom')
                ->setParameter('nom', '%'.$nom.'%');
        }

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
