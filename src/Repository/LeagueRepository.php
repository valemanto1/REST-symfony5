<?php

namespace App\Repository;

use App\Entity\League;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method League|null find($id, $lockMode = null, $lockVersion = null)
 * @method League|null findOneBy(array $criteria, array $orderBy = null)
 * @method League[]    findAll()
 * @method League[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LeagueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $manager)
    {
        parent::__construct($registry, League::class);
        $this->manager = $manager;
    }
    
    public function saveLeague($name, $code, $areaName)
    {
        $new = new League();
        $new
            ->setName($name)
            ->setCode($code)
            ->setAreaName($areaName);

        $this->manager->persist($new);
        $this->manager->flush();
        return $new;
    }

    // /**
    //  * @return League[] Returns an array of League objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?League
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
