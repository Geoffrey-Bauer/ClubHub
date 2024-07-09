<?php

namespace App\Controller;

use App\Entity\Player;
use App\Form\PlayerType;
use App\Repository\PlayerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PlayerController extends AbstractController
{
    #[Route('/player/new', name: 'player_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $player = new Player();
        $form = $this->createForm(PlayerType::class, $player);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($player);
            $em->flush();

            return $this->redirectToRoute('player_list');
        }

        return $this->render('gestion/player/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/player/list', name: 'player_list')]
    public function list(PlayerRepository $playerRepository): Response
    {
        // Récupérer tous les joueurs de la base de données
        $players = $playerRepository->findAllPlayers();

        // Rendre la vue avec les joueurs
        return $this->render('gestion/player/list.html.twig', [
            'players' => $players,
        ]);
    }

    #[Route('/player/edit/{id}', name: 'player_edit')]
    public function edit(Request $request, Player $player, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(PlayerType::class, $player);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('player_list');
        }

        return $this->render('gestion/player/edit.html.twig', [
            'form' => $form->createView(),
            'player' => $player,
        ]);
    }

    #[Route('/player/delete/{id}', name: 'player_delete')]
    public function delete(Player $player, EntityManagerInterface $em): RedirectResponse
    {
        $em->remove($player);
        $em->flush();

        $this->addFlash('success', 'Le joueur a été supprimé avec succès.');

        return $this->redirectToRoute('player_list');
    }

    // Ajoutez d'autres actions si nécessaire...
}
