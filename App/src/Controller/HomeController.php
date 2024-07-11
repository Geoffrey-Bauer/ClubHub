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
    $futureBattles = $battleRepository->createQueryBuilder('b')
      ->where('b.date > :now')
      ->setParameter('now', $now)
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
      'battles' => $futureBattles,
      'trainings' => $upcomingTrainings,
    ]);
  }
}