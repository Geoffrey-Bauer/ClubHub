<?php

namespace App\Repository;

use App\Entity\Battle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Battle>
 */
// Déclare la classe BattleRepository qui hérite de ServiceEntityRepository
class BattleRepository extends ServiceEntityRepository
{
  // Constructeur de la classe BattleRepository
  public function __construct(ManagerRegistry $registry)
  {
    // Appelle le constructeur de la classe parente avec le registre et la classe Battle
    parent::__construct($registry, Battle::class);
  }

  // Méthode pour récupérer toutes les batailles
  public function findAllBattles(): array
  {
    // Crée un QueryBuilder pour la classe Battle avec l'alias 't'
    return $this->createQueryBuilder('t')
      // Crée la requête
      ->getQuery()
      // Exécute la requête et retourne les résultats
      ->getResult();
  }

  // Méthode pour trouver des batailles par équipe
  public function findByTeam(?int $teamId)
  {
    // Crée un QueryBuilder pour la classe Battle avec l'alias 'b'
    $query = $this->createQueryBuilder('b')
      // Jointure avec l'entité Team pour l'équipe à domicile
      ->join('b.teamDomicile', 't1')
      // Jointure avec l'entité Team pour l'équipe à l'extérieur
      ->join('b.teamExterieur', 't2');

    // Si un identifiant d'équipe est fourni
    if ($teamId !== null) {
      // Ajoute une condition pour filtrer les batailles par équipe à domicile ou à l'extérieur
      $query->andWhere('t1.id = :teamId OR t2.id = :teamId')
        // Définit le paramètre 'teamId' pour la requête
        ->setParameter('teamId', $teamId);
    }

    // Crée la requête
    return $query->getQuery()
      // Exécute la requête et retourne les résultats
      ->getResult();
  }

  // Méthode pour obtenir les statistiques des joueurs par équipe pour une bataille donnée
  public function getPlayerStatsByTeam(Battle $battle, $team): array
  {
    // Vérifie si l'argument 'team' est une instance de Team ou de son proxy
    if (!$team instanceof Team && !$team instanceof \Proxies\__CG__\App\Entity\Team) {
      // Lance une exception si l'argument 'team' n'est pas valide
      throw new \InvalidArgumentException('Le deuxième argument doit être une instance de App\Entity\Team ou Proxies\__CG__\App\Entity\Team');
    }

    // Crée un QueryBuilder pour la classe Battle avec l'alias 'b'
    return $this->createQueryBuilder('b')
      // Sélectionne les statistiques des joueurs
      ->select('ps')
      // Jointure avec l'entité PlayerStats
      ->join('b.playerStats', 'ps')
      // Ajoute une condition pour filtrer par bataille
      ->where('b = :battle')
      // Ajoute une condition pour filtrer par équipe
      ->andWhere('ps.team = :team')
      // Définit le paramètre 'battle' pour la requête
      ->setParameter('battle', $battle)
      // Définit le paramètre 'team' pour la requête
      ->setParameter('team', $team)
      // Crée la requête
      ->getQuery()
      // Exécute la requête et retourne les résultats
      ->getResult();
  }

  // Méthode pour trouver une bataille avec les équipes associées par identifiant
  public function findBattleWithTeams($id): ?Battle
  {
    // Crée un QueryBuilder pour la classe Battle avec l'alias 'b'
    return $this->createQueryBuilder('b')
      // Jointure avec l'entité Team pour l'équipe à domicile
      ->join('b.teamDomicile', 'td')
      // Jointure avec l'entité Team pour l'équipe à l'extérieur
      ->join('b.teamExterieur', 'te')
      // Ajoute une condition pour filtrer par identifiant de bataille
      ->where('b.id = :id')
      // Définit le paramètre 'id' pour la requête
      ->setParameter('id', $id)
      // Crée la requête
      ->getQuery()
      // Exécute la requête et retourne un seul résultat ou null
      ->getOneOrNullResult();
  }
}
