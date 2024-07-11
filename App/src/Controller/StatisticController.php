<?php

namespace App\Controller;

use App\Entity\Battle;
use App\Entity\Player;
use App\Entity\Stats;
use App\Entity\Team;
use App\Form\StatsType;
use App\Repository\BattleRepository;
use App\Repository\StatsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class StatisticController extends AbstractController
{
  private $entityManager;

  public function __construct(EntityManagerInterface $entityManager)
  {
    $this->entityManager = $entityManager;
  }


  #[Route('/player/{id}/stats', name: 'show_player_stats')]
  public function showPlayerStats(Player $player, StatsRepository $statsRepository): Response
  {
    // Récupérez toutes les statistiques pour ce joueur
    $playerStats = $statsRepository->findBy(['player' => $player]);

    // Calculez les statistiques totales
    $totalStats = [
      'goals' => 0,
      'assists' => 0,
      'yellowCards' => 0,
      'redCards' => 0,
      'matches' => count($playerStats),
    ];

    foreach ($playerStats as $stat) {
      $totalStats['goals'] += $stat->getGoal();
      $totalStats['assists'] += $stat->getAssists();
      $totalStats['yellowCards'] += $stat->getYellowCard();
      $totalStats['redCards'] += $stat->getRedCard();
    }

    return $this->render('stats/player/show.html.twig', [
      'player' => $player,
      'playerStats' => $playerStats,
      'totalStats' => $totalStats,
    ]);
  }
  #[Route('/match/{id}/stats', name: 'show_match_stats')]
  public function showMatchStats(Battle $battle, BattleRepository $battleRepository, StatsRepository $statsRepository): Response
  {
    $teamDomicile = $battle->getTeamDomicile();
    $teamExterieur = $battle->getTeamExterieur();

    // Vérifiez si les équipes sont chargées
    if ($teamDomicile->getName() === null || $teamExterieur->getName() === null) {
      // Si les noms des équipes ne sont pas chargés, utilisez le repository pour les récupérer
      $teamDomicile = $this->getDoctrine()
        ->getRepository(Team::class)
        ->find($teamDomicile->getId());

      $teamExterieur = $this->getDoctrine()
        ->getRepository(Team::class)
        ->find($teamExterieur->getId());
    }

    // Récupérez les joueurs de chaque équipe
    $playersDomicile = $teamDomicile->getPlayers();
    $playersExterieur = $teamExterieur->getPlayers();

    // Récupérez toutes les statistiques pour ce match spécifique
    $allStats = $statsRepository->findBy(['battle' => $battle]);

    // Organisez les statistiques par équipe et par joueur
    $statsDomicile = [];
    $statsExterieur = [];

    foreach ($allStats as $stat) {
      $player = $stat->getPlayer();
      if ($player->getTeam() === $teamDomicile) {
        $statsDomicile[$player->getId()] = $stat;
      } elseif ($player->getTeam() === $teamExterieur) {
        $statsExterieur[$player->getId()] = $stat;
      }
    }

    // Créez des statistiques vides pour les joueurs qui n'en ont pas encore
    foreach ($playersDomicile as $player) {
      if (!isset($statsDomicile[$player->getId()])) {
        $newStat = new Stats();
        $newStat->setPlayer($player);
        $newStat->setBattle($battle);
        $newStat->setGoal(0);
        $newStat->setAssists(0);
        $newStat->setYellowCard(0);
        $newStat->setRedCard(0);
        $statsRepository->save($newStat, true);
        $statsDomicile[$player->getId()] = $newStat;
      }
    }

    foreach ($playersExterieur as $player) {
      if (!isset($statsExterieur[$player->getId()])) {
        $newStat = new Stats();
        $newStat->setPlayer($player);
        $newStat->setBattle($battle);
        $newStat->setGoal(0);
        $newStat->setAssists(0);
        $newStat->setYellowCard(0);
        $newStat->setRedCard(0);
        $statsRepository->save($newStat, true);
        $statsExterieur[$player->getId()] = $newStat;
      }
    }

    return $this->render('stats/match/show.html.twig', [
      'battle' => $battle,
      'playersDomicile' => $playersDomicile,
      'teamDomicile' => $teamDomicile,
      'teamExterieur' => $teamExterieur,
      'playersExterieur' => $playersExterieur,
      'statsDomicile' => $statsDomicile,
      'statsExterieur' => $statsExterieur,
    ]);
  }

  #[Route('/match/{battle}/player/{player}/edit-stats', name: 'edit_player_stats', methods: ['POST'])]
  public function editPlayerStats(Request $request, Battle $battle, Player $player, StatsRepository $statsRepository): JsonResponse
  {
    $stats = $statsRepository->findOneBy(['battle' => $battle, 'player' => $player]);

    if (!$stats) {
      $stats = new Stats();
      $stats->setBattle($battle);
      $stats->setPlayer($player);
    }

    $oldGoal = $stats->getGoal();
    $stats->setGoal($request->request->getInt('goal'));
    $stats->setAssists($request->request->getInt('assists'));
    $stats->setYellowCard($request->request->getInt('yellowCard'));
    $stats->setRedCard($request->request->getInt('redCard'));

    $statsRepository->save($stats, true);

    $battle->updateScore($player, $oldGoal, $stats->getGoal());
    $this->entityManager->flush();

    return $this->json([
      'success' => true,
      'stats' => [
        'goal' => $stats->getGoal(),
        'assists' => $stats->getAssists(),
        'yellowCard' => $stats->getYellowCard(),
        'redCard' => $stats->getRedCard(),
      ],
      'scoreDomicile' => $battle->getScoreDomicile(),
      'scoreExterieur' => $battle->getScoreExterieur(),
    ]);
  }
}
