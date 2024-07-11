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

  public function getPlayerStatsByTeam(Battle $battle, $team): array
  {
    if (!$team instanceof Team && !$team instanceof \Proxies\__CG__\App\Entity\Team) {
      throw new \InvalidArgumentException('Le deuxième argument doit être une instance de App\Entity\Team ou Proxies\__CG__\App\Entity\Team');
    }

    return $this->createQueryBuilder('b')
      ->select('ps')
      ->join('b.playerStats', 'ps')
      ->where('b = :battle')
      ->andWhere('ps.team = :team')
      ->setParameter('battle', $battle)
      ->setParameter('team', $team)
      ->getQuery()
      ->getResult();
  }

  public function findBattleWithTeams($id): ?Battle
  {
    return $this->createQueryBuilder('b')
      ->join('b.teamDomicile', 'td')
      ->join('b.teamExterieur', 'te')
      ->where('b.id = :id')
      ->setParameter('id', $id)
      ->getQuery()
      ->getOneOrNullResult();
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
