<?php

namespace App\Controller;

use App\Entity\Team;
use App\Entity\Battle;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// Déclare la classe ClassementController qui hérite d'AbstractController
class ClassementController extends AbstractController
{
  // Déclare une route pour la page de classement
  #[Route('/classement', name: 'app_classement')]
  public function index(EntityManagerInterface $entityManager): Response
  {
    // Récupère toutes les équipes depuis la base de données
    $teams = $entityManager->getRepository(Team::class)->findAll();

    // Initialise un tableau pour le classement
    $classement = [];

    // Crée un objet DateTime pour la date et l'heure actuelles en Europe/Paris
    $now = new \DateTime('now', new \DateTimeZone('Europe/Paris'));

    // Parcourt chaque équipe pour calculer ses statistiques
    foreach ($teams as $team) {
      // Calcule les statistiques de l'équipe actuelle
      $stats = $this->calculateTeamStats($entityManager, $team, $now);
      // Ajoute les statistiques calculées au tableau de classement
      $classement[] = $stats;
    }

    // Trie le classement par points décroissants, puis par différence de buts et enfin par buts marqués
    usort($classement, function ($a, $b) {
      // Compare les points des équipes
      if ($a['points'] !== $b['points']) {
        // Trie par points décroissants
        return $b['points'] <=> $a['points'];
      }
      // Calcule la différence de buts pour chaque équipe
      $diffA = $a['butsPour'] - $a['butsContre'];
      $diffB = $b['butsPour'] - $b['butsContre'];
      // Compare la différence de buts des équipes
      if ($diffA !== $diffB) {
        // Trie par différence de buts décroissante
        return $diffB <=> $diffA;
      }
      // Trie par buts marqués décroissants
      return $b['butsPour'] <=> $a['butsPour'];
    });

    // Attribue la position à chaque équipe dans le classement
    foreach ($classement as $index => $item) {
      // La position est l'index du tableau + 1
      $classement[$index]['position'] = $index + 1;
    }

    // Rend la vue 'classement/classement.html.twig' avec les données nécessaires
    return $this->render('classement/classement.html.twig', [
      // Passe le tableau de classement à la vue
      'classement' => $classement,
      // Passe la date et l'heure actuelles à la vue
      'currentDate' => $now,
    ]);
  }

  // Méthode privée pour calculer les statistiques d'une équipe
  private function calculateTeamStats(EntityManagerInterface $entityManager, Team $team, \DateTime $now): array
  {
    // Initialise les statistiques de l'équipe
    $points = 0;
    $victoires = 0;
    $egalites = 0;
    $defaites = 0;
    $butsPour = 0;
    $butsContre = 0;
    $matchsJoues = 0;

    // Récupère tous les matchs de l'équipe à domicile
    $allMatches = $entityManager->getRepository(Battle::class)->findBy([
      'teamDomicile' => $team,
    ]);
    // Récupère tous les matchs de l'équipe à l'extérieur et les fusionne avec ceux à domicile
    $allMatches = array_merge($allMatches, $entityManager->getRepository(Battle::class)->findBy([
      'teamExterieur' => $team,
    ]));

    // Parcourt tous les matchs pour calculer les statistiques
    foreach ($allMatches as $match) {
      // Récupère la date du match
      $matchDate = $match->getDate();
      // Calcule l'heure de fin du match (90 minutes après le début)
      $matchEndTime = (clone $matchDate)->modify('+90 minutes');
      // Vérifie si l'équipe actuelle est l'équipe à domicile
      $isHomeTeam = $match->getTeamDomicile() === $team;

      // Si le match est terminé
      if ($now > $matchEndTime) {
        // Traite le match terminé pour mettre à jour les statistiques
        $this->processFinishedMatch($match, $isHomeTeam, $points, $victoires, $egalites, $defaites, $butsPour, $butsContre, $matchsJoues);
      }
      // Si le match est en cours
      elseif ($now >= $matchDate && $now <= $matchEndTime) {
        // Traite le match en cours pour mettre à jour les statistiques
        $this->processOngoingMatch($match, $isHomeTeam, $points, $victoires, $egalites, $defaites, $butsPour, $butsContre, $matchsJoues);
      }
      // Si le match n'a pas encore commencé, ne rien faire
    }

    // Retourne les statistiques de l'équipe
    return [
      'nom' => $team->getName(),
      'points' => $points,
      'victoires' => $victoires,
      'egalites' => $egalites,
      'defaites' => $defaites,
      'butsPour' => $butsPour,
      'butsContre' => $butsContre,
      'matchsJoues' => $matchsJoues,
    ];
  }

  // Méthode privée pour traiter un match terminé
  private function processFinishedMatch(Battle $match, bool $isHomeTeam, int &$points, int &$victoires, int &$egalites, int &$defaites, int &$butsPour, int &$butsContre, int &$matchsJoues): void
  {
    // Récupère le score de l'équipe actuelle
    $teamScore = $isHomeTeam ? $match->getScoreDomicile() : $match->getScoreExterieur();
    // Récupère le score de l'équipe adverse
    $opponentScore = $isHomeTeam ? $match->getScoreExterieur() : $match->getScoreDomicile();

    // Ajoute les buts marqués par l'équipe actuelle
    $butsPour += $teamScore;
    // Ajoute les buts encaissés par l'équipe actuelle
    $butsContre += $opponentScore;
    // Incrémente le nombre de matchs joués
    $matchsJoues++;

    // Si l'équipe actuelle a gagné
    if ($teamScore > $opponentScore) {
      // Ajoute 3 points pour une victoire
      $points += 3;
      // Incrémente le nombre de victoires
      $victoires++;
    }
    // Si l'équipe actuelle a perdu
    elseif ($teamScore < $opponentScore) {
      // Incrémente le nombre de défaites
      $defaites++;
    }
    // Si le match est nul
    else {
      // Ajoute 1 point pour un match nul
      $points += 1;
      // Incrémente le nombre de matchs nuls
      $egalites++;
    }
  }

  // Méthode privée pour traiter un match en cours
  private function processOngoingMatch(Battle $match, bool $isHomeTeam, int &$points, int &$victoires, int &$egalites, int &$defaites, int &$butsPour, int &$butsContre, int &$matchsJoues): void
  {
    // Récupère le score de l'équipe actuelle
    $teamScore = $isHomeTeam ? $match->getScoreDomicile() : $match->getScoreExterieur();
    // Récupère le score de l'équipe adverse
    $opponentScore = $isHomeTeam ? $match->getScoreExterieur() : $match->getScoreDomicile();

    // Ajoute les buts marqués par l'équipe actuelle
    $butsPour += $teamScore;
    // Ajoute les buts encaissés par l'équipe actuelle
    $butsContre += $opponentScore;
    // Incrémente le nombre de matchs joués
    $matchsJoues++;

    // Si l'équipe actuelle mène au score
    if ($teamScore > $opponentScore) {
      // Ajoute 3 points potentiels pour une victoire
      $points += 3;
    }
    // Si le score est égal
    elseif ($teamScore === $opponentScore) {
      // Ajoute 1 point potentiel pour un match nul
      $points += 1;
    }
  }
}