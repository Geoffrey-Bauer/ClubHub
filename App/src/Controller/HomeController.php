<?php

namespace App\Controller;

use App\Repository\BattleRepository;
use App\Repository\TrainingRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

// Déclare la classe HomeController qui hérite d'AbstractController
class HomeController extends AbstractController
{
  // Déclare une route pour la page d'accueil
  #[Route('/', name: 'app_home')]
  public function index(BattleRepository $battleRepository, TrainingRepository $trainingRepository): Response
  {
    // Crée un objet DateTime pour la date et l'heure actuelles
    $now = new \DateTime();

    // Crée un objet DateTime pour le début de la journée actuelle (00:00:00)
    $today = new \DateTime('today');

    // Crée un objet DateTime pour le début de la journée suivante (00:00:00)
    $tomorrow = (new \DateTime('today'))->modify('+1 day');

    // Récupère les batailles prévues pour aujourd'hui
    $battles = $battleRepository->createQueryBuilder('b')
      // Filtre les batailles à partir d'aujourd'hui
      ->where('b.date >= :today')
      // Jusqu'à demain (exclu)
      ->andWhere('b.date < :tomorrow')
      // Définit le paramètre 'today' pour la requête
      ->setParameter('today', $today)
      // Définit le paramètre 'tomorrow' pour la requête
      ->setParameter('tomorrow', $tomorrow)
      // Trie les résultats par date croissante
      ->orderBy('b.date', 'ASC')
      // Crée la requête
      ->getQuery()
      // Exécute la requête et retourne les résultats
      ->getResult();

    // Récupère les prochaines batailles après demain
    $upcomingBattles = $battleRepository->createQueryBuilder('b')
      // Filtre les batailles après demain
      ->where('b.date > :tomorrow')
      // Définit le paramètre 'tomorrow' pour la requête
      ->setParameter('tomorrow', $tomorrow)
      // Trie les résultats par date croissante
      ->orderBy('b.date', 'ASC')
      // Limite les résultats à 5 batailles
      ->setMaxResults(5)
      // Crée la requête
      ->getQuery()
      // Exécute la requête et retourne les résultats
      ->getResult();

    // Récupère les prochains entraînements à partir de maintenant
    $upcomingTrainings = $trainingRepository->createQueryBuilder('t')
      // Filtre les entraînements à partir de maintenant
      ->where('t.date >= :now')
      // Définit le paramètre 'now' pour la requête
      ->setParameter('now', $now)
      // Trie les résultats par date croissante
      ->orderBy('t.date', 'ASC')
      // Limite les résultats à 5 entraînements
      ->setMaxResults(5)
      // Crée la requête
      ->getQuery()
      // Exécute la requête et retourne les résultats
      ->getResult();

    // Rend la vue 'home/home.html.twig' avec les données nécessaires
    return $this->render('home/home.html.twig', [
      // Nom du contrôleur pour la vue
      'controller_name' => 'HomeController',
      // Batailles du jour
      'battles' => $battles,
      // Prochaines batailles
      'upcomingBattles' => $upcomingBattles,
      // Prochains entraînements
      'trainings' => $upcomingTrainings,
    ]);
  }
}