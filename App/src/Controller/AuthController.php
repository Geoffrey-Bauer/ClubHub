<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

// Déclare la classe AuthController qui hérite d'AbstractController
class AuthController extends AbstractController
{
  // Déclare une route pour l'inscription avec les méthodes GET et POST
  #[Route('/register', name: 'app_register', methods: ['GET', 'POST'])]
  public function register(Request $request, UserPasswordHasherInterface $passwordHasher, ManagerRegistry $managerRegistry, Security $security): Response
  {
    // Vérifie si l'utilisateur est déjà authentifié
    if ($security->isGranted('IS_AUTHENTICATED_FULLY')) {
      // Redirige vers la page d'accueil si l'utilisateur est déjà connecté
      return $this->redirectToRoute('app_home');
    }

    // Crée une nouvelle instance de l'entité User
    $user = new User();
    // Crée le formulaire d'inscription pour l'utilisateur
    $form = $this->createForm(UserType::class, $user);
    // Gère la requête du formulaire
    $form->handleRequest($request);

    // Vérifie si le formulaire est soumis et valide
    if ($form->isSubmitted() && $form->isValid()) {
      // Hash le mot de passe de l'utilisateur
      $hashedPassword = $passwordHasher->hashPassword(
        $user,
        $form->get('password')->getData()
      );
      // Définit le mot de passe hashé pour l'utilisateur
      $user->setPassword($hashedPassword);

      // Obtient le gestionnaire d'entités de doctrine
      $entityManager = $managerRegistry->getManager();
      // Persiste l'utilisateur dans la base de données
      $entityManager->persist($user);
      // Sauvegarde les modifications dans la base de données
      $entityManager->flush();

      // Ajoute un message flash pour indiquer que le compte a été créé avec succès
      $this->addFlash('success', 'Votre compte a été créé avec succès!');

      // Redirige vers la page de connexion
      return $this->redirectToRoute('app_login');
    }

    // Rend la vue du formulaire d'inscription
    return $this->render('auth/register.html.twig', [
      'registrationForm' => $form->createView(),
    ]);
  }

  // Déclare une route pour la connexion
  #[Route('/login', name: 'app_login')]
  public function login(AuthenticationUtils $authenticationUtils, Security $security): Response
  {
    // Vérifie si l'utilisateur est déjà authentifié
    if ($security->isGranted('IS_AUTHENTICATED_FULLY')) {
      // Redirige vers la page d'accueil si l'utilisateur est déjà connecté
      return $this->redirectToRoute('app_home');
    }

    // Obtient la dernière erreur d'authentification s'il y en a une
    $error = $authenticationUtils->getLastAuthenticationError();
    // Obtient le dernier nom d'utilisateur saisi
    $lastUsername = $authenticationUtils->getLastUsername();

    // Rend la vue du formulaire de connexion
    return $this->render('auth/login.html.twig', [
      'last_username' => $lastUsername,
      'error' => $error,
    ]);
  }

  // Déclare une route pour la déconnexion
  #[Route('/logout', name: 'app_logout')]
  public function logout(): void
  {
    // Cette méthode peut rester vide,
    // car la déconnexion est gérée par Symfony
  }
}