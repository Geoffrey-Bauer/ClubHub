<?php

namespace App\Controller;

use App\Entity\Player;
use App\Form\PlayerType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

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
    public function list(EntityManagerInterface $em): Response
    {
        $players = $em->getRepository(Player::class)->findAll();

        // Utilisation de dump() pour vérifier le contenu de $players
        dump($players); // Vérifiez la sortie de cette instruction dans votre console

        return $this->render('gestion/player/list.html.twig', [
            'players' => $players,
        ]);
    }

    // Ajoutez d'autres actions si nécessaire...
}
