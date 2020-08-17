<?php

namespace App\Repository;

use App\Entity\Player;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method Player|null find($id, $lockMode = null, $lockVersion = null)
 * @method Player|null findOneBy(array $criteria, array $orderBy = null)
 * @method Player[]    findAll()
 * @method Player[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlayerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $manager)
    {
        parent::__construct($registry, Player::class);
        $this->manager = $manager;
    }
    
    public function savePlayer($name, $position, $dateOfBirth, $countryOfBirth, $nationality, $team)
    {
        $new = new Player();
        $new
            ->setName($name)
            ->setPosition($position == null ? "-" : $position)
            ->setDateOfBirth($dateOfBirth)
            ->setCountryOfBirth($countryOfBirth)
            ->setNationality($nationality)
            ->setTeam($team);

        $this->manager->persist($new);
        $this->manager->flush();
    }
    
    public function countAll($leagueCode)
    {
        return $this->createQueryBuilder('p')
            ->select('count(p.id)')
            ->innerJoin("p.team", "t")
            ->innerJoin("t.leagues", "l")   
            ->where('l.code = :code')
            ->setParameter('code', $leagueCode)
            ->getQuery()
            ->getSingleScalarResult();
    }

    // /**
    //  * @return Player[] Returns an array of Player objects
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
    public function findOneBySomeField($value): ?Player
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
