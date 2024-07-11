<?php

namespace App\Controller;

use App\Entity\Battle;
use App\Entity\Training;
use App\Form\BattleType;
use App\Form\TrainingType;
use App\Repository\BattleRepository;
use App\Repository\StatsRepository;
use App\Repository\TeamRepository;
use App\Repository\TrainingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PlanningController extends AbstractController
{
  #[Route('/planning', name: 'planning')]
  public function index(TeamRepository $teamRepository, BattleRepository $battleRepository): Response
  {
    return $this->render('planning/planning.html.twig', [
      'teams' => $teamRepository->findAllTeams(),
      'battles' => $battleRepository->findAllBattles()
    ]);
  }

  // TRAINING SECTION START

  #[Route('/planning/training/new', name: 'planning_training_new')]
  public function newTraining(Request $request, EntityManagerInterface $em): Response
  {
    $training = new Training();
    $form = $this->createForm(TrainingType::class, $training);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $em->persist($training);
      $em->flush();
      return $this->redirectToRoute('planning_training_list');
    }

    return $this->render('planning/training/new.html.twig', [
      'form' => $form->createView(),
    ]);
  }

  #[Route('/planning/training/list', name: 'planning_training_list')]
  public function listTrainings(TrainingRepository $trainingRepository): Response
  {
    return $this->render('planning/training/list.html.twig', [
      'trainings' => $trainingRepository->findAll(),
    ]);
  }

  #[Route('/planning/training/{id}/edit', name: 'training_edit')]
  public function editTraining(Request $request, Training $training, EntityManagerInterface $em): Response
  {
    $form = $this->createForm(TrainingType::class, $training);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $em->flush();
      $this->addFlash('success', 'L\'entraînement a été mis à jour avec succès.');
      return $this->redirectToRoute('planning_training_list');
    }

    return $this->render('planning/training/edit.html.twig', [
      'training' => $training,
      'form' => $form->createView(),
    ]);
  }

  #[Route('/planning/training/{id}/delete', name: 'training_delete')]
  public function deleteTraining(Request $request, Training $training, EntityManagerInterface $em): Response
  {
    if ($this->isCsrfTokenValid('delete'.$training->getId(), $request->request->get('_token'))) {
      $em->remove($training);
      $em->flush();
      $this->addFlash('success', 'L\'entraînement a été supprimé avec succès.');
    } else {
      $this->addFlash('error', 'Token CSRF invalide.');
    }

    return $this->redirectToRoute('planning_training_list');
  }

  // TRAINING SECTION END

  // MATCH SECTION START

  #[Route('/planning/match/new', name: 'planning_match_new')]
  public function newMatch(Request $request, EntityManagerInterface $em): Response
  {
    $battle = new Battle();
    $form = $this->createForm(BattleType::class, $battle);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $em->persist($battle);
      $em->flush();
      return $this->redirectToRoute('planning_match_list');
    }

    return $this->render('planning/match/new.html.twig', [
      'form' => $form->createView(),
    ]);
  }

  #[Route('/planning/match/list', name: 'planning_match_list')]
  public function listMatches(Request $request, BattleRepository $battleRepository, TeamRepository $teamRepository): Response
  {
    $teamFilter = $request->query->get('teamFilter') ? (int) $request->query->get('teamFilter') : null;

    return $this->render('planning/match/list.html.twig', [
      'battles' => $battleRepository->findByTeam($teamFilter),
      'teams' => $teamRepository->findAll(),
    ]);
  }

  #[Route('/planning/match/{id}/edit', name: 'battle_edit')]
  public function editMatch(Request $request, Battle $battle, EntityManagerInterface $em): Response
  {
    $form = $this->createForm(BattleType::class, $battle);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $em->flush();
      $this->addFlash('success', 'Le match a été mis à jour avec succès.');
      return $this->redirectToRoute('planning_match_list');
    }

    return $this->render('planning/match/edit.html.twig', [
      'battle' => $battle,
      'form' => $form->createView(),
    ]);
  }

  #[Route('/planning/match/{id}/delete', name: 'battle_delete')]
  public function deleteMatch(Request $request, Battle $battle, EntityManagerInterface $em, StatsRepository $statsRepository): Response
  {
    if ($this->isCsrfTokenValid('delete'.$battle->getId(), $request->request->get('_token'))) {
      try {
        // Récupérer toutes les statistiques liées à ce match
        $stats = $statsRepository->findBy(['battle' => $battle]);

        // Supprimer chaque statistique
        foreach ($stats as $stat) {
          $em->remove($stat);
        }

        // Supprimer le match
        $em->remove($battle);

        // Appliquer les changements dans la base de données
        $em->flush();

        $this->addFlash('success', 'Le match et toutes ses statistiques ont été supprimés avec succès.');
      } catch (\Exception $e) {
        $this->addFlash('error', 'Une erreur est survenue lors de la suppression du match : ' . $e->getMessage());
      }
    } else {
      $this->addFlash('error', 'Token CSRF invalide.');
    }

    return $this->redirectToRoute('planning_match_list');
  }

  // MATCH SECTION END
}
