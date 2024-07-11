<?php

namespace App\Controller;

use App\Entity\Battle;
use App\Entity\Player;
use App\Entity\Stats;
use App\Repository\BattleRepository;
use App\Repository\StatsRepository;
use App\Repository\PlayerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;

class StatisticController extends AbstractController
{
  private const EVENT_GOAL = 'goal';
  private const EVENT_YELLOW_CARD = 'yellowCard';
  private const EVENT_RED_CARD = 'redCard';
  private const MATCH_DURATION = 90;
  private const HALF_TIME_DURATION = 15;

  private $entityManager;
  private $statsRepository;
  private $battleRepository;
  private $playerRepository;

  public function __construct(
    EntityManagerInterface $entityManager,
    StatsRepository $statsRepository,
    BattleRepository $battleRepository,
    PlayerRepository $playerRepository
  ) {
    $this->entityManager = $entityManager;
    $this->statsRepository = $statsRepository;
    $this->battleRepository = $battleRepository;
    $this->playerRepository = $playerRepository;
  }

  #[Route('/match/{id}/stats', name: 'show_match_stats')]
  public function showMatchStats(Battle $battle): Response
  {
    $teamDomicile = $battle->getTeamDomicile();
    $teamExterieur = $battle->getTeamExterieur();

    $playersDomicile = $teamDomicile->getPlayers();
    $playersExterieur = $teamExterieur->getPlayers();

    $allStats = $this->statsRepository->findBy(['battle' => $battle]);

    if (empty($allStats)) {
      $this->generateMatchStats($battle);
      $allStats = $this->statsRepository->findBy(['battle' => $battle]);
    }

    $statsDomicile = $this->organizeStatsByTeam($allStats, $teamDomicile);
    $statsExterieur = $this->organizeStatsByTeam($allStats, $teamExterieur);

    $events = $this->getEventsFromStats($allStats);

    return $this->render('stats/match/show.html.twig', [
      'battle' => $battle,
      'playersDomicile' => $playersDomicile,
      'teamDomicile' => $teamDomicile,
      'teamExterieur' => $teamExterieur,
      'playersExterieur' => $playersExterieur,
      'statsDomicile' => $statsDomicile,
      'statsExterieur' => $statsExterieur,
      'events' => $events,
    ]);
  }

  private function generateMatchStats(Battle $battle)
  {
    $playersDomicile = $battle->getTeamDomicile()->getPlayers();
    $playersExterieur = $battle->getTeamExterieur()->getPlayers();

    $startTime = $battle->getDate();
    $endTime = (clone $startTime)->modify('+' . self::MATCH_DURATION . ' minutes');

    $events = $this->generateRandomEvents($playersDomicile, $playersExterieur, $startTime, $endTime);

    foreach ($events as $event) {
      $stat = new Stats();
      $stat->setPlayer($event['player']);
      $stat->setBattle($battle);
      $stat->setTime($event['time']->format('H:i'));

      switch ($event['type']) {
        case self::EVENT_GOAL:
          $stat->setGoal(1);
          $this->updateScore($battle, $event['player']);
          break;
        case self::EVENT_YELLOW_CARD:
          $stat->setYellowCard(1);
          break;
        case self::EVENT_RED_CARD:
          $stat->setRedCard(1);
          break;
      }

      $this->entityManager->persist($stat);
    }

    $this->entityManager->flush();
  }

  private function generateRandomEvents($playersDomicile, $playersExterieur, \DateTime $startTime, \DateTime $endTime): array
  {
    $events = [];
    $allPlayers = array_merge($playersDomicile->toArray(), $playersExterieur->toArray());

    $currentTime = clone $startTime;
    $halfTimeStart = (clone $startTime)->modify('+45 minutes');
    $halfTimeEnd = (clone $halfTimeStart)->modify('+' . self::HALF_TIME_DURATION . ' minutes');

    while ($currentTime <= $endTime) {
      if ($currentTime >= $halfTimeStart && $currentTime < $halfTimeEnd) {
        $currentTime = clone $halfTimeEnd;
        continue;
      }

      if (rand(1, 100) <= 10) { // 10% chance d'un événement
        $player = $allPlayers[array_rand($allPlayers)];
        $eventType = $this->getRandomEventType();

        $events[] = [
          'type' => $eventType,
          'player' => $player,
          'time' => clone $currentTime,
        ];
      }
      $currentTime->modify('+1 minute');
    }

    usort($events, function($a, $b) {
      return $a['time'] <=> $b['time'];
    });

    return $events;
  }

  private function getRandomEventType(): string
  {
    $eventTypes = [self::EVENT_GOAL, self::EVENT_YELLOW_CARD, self::EVENT_RED_CARD];
    return $eventTypes[array_rand($eventTypes)];
  }

  private function updateScore(Battle $battle, Player $player)
  {
    if ($player->getTeam() === $battle->getTeamDomicile()) {
      $battle->setScoreDomicile($battle->getScoreDomicile() + 1);
    } else {
      $battle->setScoreExterieur($battle->getScoreExterieur() + 1);
    }
    $this->entityManager->persist($battle);
  }

  private function organizeStatsByTeam($allStats, $team): array
  {
    $teamStats = [];
    foreach ($allStats as $stat) {
      if ($stat->getPlayer()->getTeam() === $team) {
        $teamStats[$stat->getPlayer()->getId()][] = $stat;
      }
    }
    return $teamStats;
  }

  private function getEventsFromStats(array $allStats): array
  {
    $events = [];
    foreach ($allStats as $stat) {
      $eventType = $this->getEventTypeFromStat($stat);
      if ($eventType) {
        $events[] = [
          'type' => $eventType,
          'player' => $stat->getPlayer()->getFirstname() . ' ' . $stat->getPlayer()->getLastname(),
          'time' => $stat->getTime(),
        ];
      }
    }

    usort($events, function($a, $b) {
      return strcmp($a['time'], $b['time']);
    });

    return $events;
  }

  private function getEventTypeFromStat(Stats $stat): ?string
  {
    if ($stat->getGoal() > 0) return self::EVENT_GOAL;
    if ($stat->getYellowCard() > 0) return self::EVENT_YELLOW_CARD;
    if ($stat->getRedCard() > 0) return self::EVENT_RED_CARD;
    return null;
  }

  #[Route('/match/{id}/edit', name: 'edit_match', methods: ['POST'])]
  public function editMatch(Request $request, Battle $battle): Response
  {
    $newEndTime = new \DateTime($request->request->get('newEndTime'));
    $startTime = $battle->getDate();
    $maxEndTime = (clone $startTime)->modify('+' . self::MATCH_DURATION . ' minutes');

    if ($newEndTime > $maxEndTime) {
      $newEndTime = $maxEndTime;
    }

    $existingStats = $this->statsRepository->findBy(['battle' => $battle]);
    $lastEventTime = !empty($existingStats) ? max(array_map(function($stat) {
      return \DateTime::createFromFormat('H:i', $stat->getTime());
    }, $existingStats)) : $startTime;

    $newEvents = $this->generateRandomEvents(
      $battle->getTeamDomicile()->getPlayers(),
      $battle->getTeamExterieur()->getPlayers(),
      $lastEventTime,
      $newEndTime
    );

    foreach ($newEvents as $event) {
      $stat = new Stats();
      $stat->setPlayer($event['player']);
      $stat->setBattle($battle);
      $stat->setTime($event['time']->format('H:i'));

      switch ($event['type']) {
        case self::EVENT_GOAL:
          $stat->setGoal(1);
          $this->updateScore($battle, $event['player']);
          break;
        case self::EVENT_YELLOW_CARD:
          $stat->setYellowCard(1);
          break;
        case self::EVENT_RED_CARD:
          $stat->setRedCard(1);
          break;
      }

      $this->entityManager->persist($stat);
    }

    $this->entityManager->flush();

    return $this->redirectToRoute('show_match_stats', ['id' => $battle->getId()]);
  }
}