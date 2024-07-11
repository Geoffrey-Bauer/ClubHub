<?php

namespace App\Controller;

use App\Repository\BattleRepository;
use App\Repository\TrainingRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
  #[Route('/', name: 'app_home')]
  public function index(BattleRepository $battleRepository, TrainingRepository $trainingRepository): Response
  {
    $now = new \DateTime();
    $today = new \DateTime('today');
    $tomorrow = (new \DateTime('today'))->modify('+1 day');

    $battles = $battleRepository->createQueryBuilder('b')
      ->where('b.date >= :today')
      ->andWhere('b.date < :tomorrow')
      ->setParameter('today', $today)
      ->setParameter('tomorrow', $tomorrow)
      ->orderBy('b.date', 'ASC')
      ->getQuery()
      ->getResult();

    $upcomingBattles = $battleRepository->createQueryBuilder('b')
      ->where('b.date > :tomorrow')
      ->setParameter('tomorrow', $tomorrow)
      ->orderBy('b.date', 'ASC')
      ->setMaxResults(5)
      ->getQuery()
      ->getResult();

    $upcomingTrainings = $trainingRepository->createQueryBuilder('t')
      ->where('t.date >= :now')
      ->setParameter('now', $now)
      ->orderBy('t.date', 'ASC')
      ->setMaxResults(5)
      ->getQuery()
      ->getResult();

    return $this->render('home/home.html.twig', [
      'controller_name' => 'HomeController',
      'battles' => $battles,
      'upcomingBattles' => $upcomingBattles,
      'trainings' => $upcomingTrainings,
    ]);
  }
}