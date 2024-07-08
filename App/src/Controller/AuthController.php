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

class AuthController extends AbstractController
{
  #[Route('/register', name: 'app_register', methods: ['GET', 'POST'])]
  public function register(Request $request, UserPasswordHasherInterface $passwordHasher, ManagerRegistry $managerRegistry, Security $security): Response
  {
    // Vérifier si l'utilisateur est déjà connecté
    if ($security->isGranted('IS_AUTHENTICATED_FULLY')) {
      return $this->redirectToRoute('app_home');
    }

    $user = new User();
    $form = $this->createForm(UserType::class, $user);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      // Encode le mot de passe
      $hashedPassword = $passwordHasher->hashPassword(
        $user,
        $form->get('password')->getData()
      );
      $user->setPassword($hashedPassword);

      // Sauvegarde l'utilisateur en base de données
      $entityManager = $managerRegistry->getManager();
      $entityManager->persist($user);
      $entityManager->flush();

      // Ajoute un message flash
      $this->addFlash('success', 'Votre compte a été créé avec succès!');

      // Redirige vers la page de connexion ou le tableau de bord
      return $this->redirectToRoute('app_login');
    }

    return $this->render('auth/register.html.twig', [
      'registrationForm' => $form->createView(),
    ]);
  }

  #[Route('/login', name: 'app_login')]
  public function login(AuthenticationUtils $authenticationUtils, Security $security): Response
  {
    // Vérifier si l'utilisateur est déjà connecté
    if ($security->isGranted('IS_AUTHENTICATED_FULLY')) {
      return $this->redirectToRoute('app_home');
    }

    $error = $authenticationUtils->getLastAuthenticationError();
    $lastUsername = $authenticationUtils->getLastUsername();

    return $this->render('auth/login.html.twig', [
      'last_username' => $lastUsername,
      'error' => $error,
    ]);
  }

  #[Route('/logout', name: 'app_logout')]
  public function logout(): void
  {
    // Cette méthode peut rester vide,
    // car la déconnexion est gérée par Symfony
  }
}