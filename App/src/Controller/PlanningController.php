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

// Déclare une route pour la création d'un nouveau match
  #[Route('/planning/match/new', name: 'planning_match_new')]
  public function newMatch(Request $request, EntityManagerInterface $em): Response
  {
    // Crée une nouvelle instance de Battle (match)
    $battle = new Battle();
    // Crée un formulaire pour le match en utilisant BattleType
    $form = $this->createForm(BattleType::class, $battle);
    // Gère la requête du formulaire
    $form->handleRequest($request);

    // Vérifie si le formulaire est soumis et valide
    if ($form->isSubmitted() && $form->isValid()) {
      // Persiste le nouveau match dans la base de données
      $em->persist($battle);
      // Applique les changements dans la base de données
      $em->flush();
      // Redirige vers la liste des matchs
      return $this->redirectToRoute('planning_match_list');
    }

    // Rend la vue pour créer un nouveau match
    return $this->render('planning/match/new.html.twig', [
      // Passe le formulaire à la vue
      'form' => $form->createView(),
    ]);
  }

// Déclare une route pour lister les matchs
  #[Route('/planning/match/list', name: 'planning_match_list')]
  public function listMatches(Request $request, BattleRepository $battleRepository, TeamRepository $teamRepository): Response
  {
    // Récupère le filtre d'équipe depuis les paramètres de la requête
    $teamFilter = $request->query->get('teamFilter') ? (int) $request->query->get('teamFilter') : null;

    // Rend la vue pour lister les matchs
    return $this->render('planning/match/list.html.twig', [
      // Passe les matchs filtrés par équipe à la vue
      'battles' => $battleRepository->findByTeam($teamFilter),
      // Passe toutes les équipes à la vue
      'teams' => $teamRepository->findAll(),
    ]);
  }

// Déclare une route pour éditer un match
  #[Route('/planning/match/{id}/edit', name: 'battle_edit')]
  public function editMatch(Request $request, Battle $battle, EntityManagerInterface $em): Response
  {
    // Crée un formulaire pour éditer le match en utilisant BattleType
    $form = $this->createForm(BattleType::class, $battle);
    // Gère la requête du formulaire
    $form->handleRequest($request);

    // Vérifie si le formulaire est soumis et valide
    if ($form->isSubmitted() && $form->isValid()) {
      // Applique les changements dans la base de données
      $em->flush();
      // Ajoute un message flash de succès
      $this->addFlash('success', 'Le match a été mis à jour avec succès.');
      // Redirige vers la liste des matchs
      return $this->redirectToRoute('planning_match_list');
    }

    // Rend la vue pour éditer le match
    return $this->render('planning/match/edit.html.twig', [
      // Passe le match et le formulaire à la vue
      'battle' => $battle,
      'form' => $form->createView(),
    ]);
  }

// Déclare une route pour supprimer un match
  #[Route('/planning/match/{id}/delete', name: 'battle_delete')]
  public function deleteMatch(Request $request, Battle $battle, EntityManagerInterface $em, StatsRepository $statsRepository): Response
  {
    // Vérifie si le token CSRF est valide
    if ($this->isCsrfTokenValid('delete'.$battle->getId(), $request->request->get('_token'))) {
      try {
        // Récupère toutes les statistiques liées à ce match
        $stats = $statsRepository->findBy(['battle' => $battle]);

        // Supprime chaque statistique
        foreach ($stats as $stat) {
          $em->remove($stat);
        }

        // Supprime le match
        $em->remove($battle);

        // Applique les changements dans la base de données
        $em->flush();

        // Ajoute un message flash de succès
        $this->addFlash('success', 'Le match et toutes ses statistiques ont été supprimés avec succès.');
      } catch (\Exception $e) {
        // Ajoute un message flash d'erreur en cas d'exception
        $this->addFlash('error', 'Une erreur est survenue lors de la suppression du match : ' . $e->getMessage());
      }
    } else {
      // Ajoute un message flash d'erreur si le token CSRF est invalide
      $this->addFlash('error', 'Token CSRF invalide.');
    }

    // Redirige vers la liste des matchs
    return $this->redirectToRoute('planning_match_list');
  }

// MATCH SECTION END
}
