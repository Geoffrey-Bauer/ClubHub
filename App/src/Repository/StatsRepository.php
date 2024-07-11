<?php

namespace App\Repository;

use App\Entity\Stats;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Stats>
 */
class StatsRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, Stats::class);
  }

  public function findByBattleId(int $battleId): array
  {
    return $this->createQueryBuilder('s')
      ->where('s.battle = :battleId')
      ->setParameter('battleId', $battleId)
      ->getQuery()
      ->getResult();
  }

  public function save(Stats $stats, bool $flush = true): void
  {
    $this->getEntityManager()->persist($stats);

    if ($flush) {
      $this->getEntityManager()->flush();
    }
  }
}
