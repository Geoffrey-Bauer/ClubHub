<?php

namespace App\Repository;

use App\Entity\Battle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Battle>
 */
class BattleRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, Battle::class);
  }

  public function findAllBattles(): array
  {
    return $this->createQueryBuilder('t')
      ->getQuery()
      ->getResult();
  }

  public function findByTeam(?int $teamId)
  {
    $query = $this->createQueryBuilder('b')
      ->join('b.teamDomicile', 't1')
      ->join('b.teamExterieur', 't2');

    if ($teamId !== null) {
      $query->andWhere('t1.id = :teamId OR t2.id = :teamId')
        ->setParameter('teamId', $teamId);
    }

    return $query->getQuery()
      ->getResult();
  }

  //    /**
  //     * @return Battle[] Returns an array of Battle objects
  //     */
  //    public function findByExampleField($value): array
  //    {
  //        return $this->createQueryBuilder('b')
  //            ->andWhere('b.exampleField = :val')
  //            ->setParameter('val', $value)
  //            ->orderBy('b.id', 'ASC')
  //            ->setMaxResults(10)
  //            ->getQuery()
  //            ->getResult()
  //        ;
  //    }

  //    public function findOneBySomeField($value): ?Battle
  //    {
  //        return $this->createQueryBuilder('b')
  //            ->andWhere('b.exampleField = :val')
  //            ->setParameter('val', $value)
  //            ->getQuery()
  //            ->getOneOrNullResult()
  //        ;
  //    }
}
