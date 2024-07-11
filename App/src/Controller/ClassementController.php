<?php

namespace App\Controller;

use App\Entity\Team;
use App\Entity\Battle;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ClassementController extends AbstractController
{
  #[Route('/classement', name: 'app_classement')]
  public function index(EntityManagerInterface $entityManager): Response
  {
    $teams = $entityManager->getRepository(Team::class)->findAll();
    $classement = [];
    $now = new \DateTime();

    foreach ($teams as $team) {
      $stats = $this->calculateTeamStats($entityManager, $team, $now);
      $classement[] = $stats;
    }

    usort($classement, function ($a, $b) {
      if ($a['points'] !== $b['points']) {
        return $b['points'] <=> $a['points'];
      }
      $diffA = $a['butsPour'] - $a['butsContre'];
      $diffB = $b['butsPour'] - $b['butsContre'];
      if ($diffA !== $diffB) {
        return $diffB <=> $diffA;
      }
      return $b['butsPour'] <=> $a['butsPour'];
    });

    foreach ($classement as $index => $item) {
      $classement[$index]['position'] = $index + 1;
    }

    return $this->render('classement/classement.html.twig', [
      'classement' => $classement,
      'currentDate' => $now,
    ]);
  }

  private function calculateTeamStats(EntityManagerInterface $entityManager, Team $team, \DateTime $now): array
  {
    $points = 0;
    $victoires = 0;
    $egalites = 0;
    $defaites = 0;
    $butsPour = 0;
    $butsContre = 0;
    $matchsJoues = 0;

    $allMatches = $entityManager->getRepository(Battle::class)->findBy([
      'teamDomicile' => $team,
    ]);
    $allMatches = array_merge($allMatches, $entityManager->getRepository(Battle::class)->findBy([
      'teamExterieur' => $team,
    ]));

    foreach ($allMatches as $match) {
      $matchDate = $match->getDate();
      $isHomeTeam = $match->getTeamDomicile() === $team;

      // Match terminé ou en cours
      if ($matchDate <= $now) {
        $teamScore = $isHomeTeam ? $match->getScoreDomicile() : $match->getScoreExterieur();
        $opponentScore = $isHomeTeam ? $match->getScoreExterieur() : $match->getScoreDomicile();

        $butsPour += $teamScore;
        $butsContre += $opponentScore;

        // Si le match est terminé (date antérieure à aujourd'hui), on compte les points
        if ($matchDate->format('Y-m-d') < $now->format('Y-m-d')) {
          $matchsJoues++;
          if ($teamScore > $opponentScore) {
            $points += 3;
            $victoires++;
          } elseif ($teamScore < $opponentScore) {
            $defaites++;
          } else {
            $points += 1;
            $egalites++;
          }
        }
      }
    }

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
}