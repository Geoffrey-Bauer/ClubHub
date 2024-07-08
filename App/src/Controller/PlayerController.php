<?php

namespace App\Controller;

use App\Entity\Player;
use App\Form\PlayerType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlayerController extends AbstractController
{

    #[Route('/player/new', name: 'new')]

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

        return $this->render('player/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // Ajoutez d'autres actions si nécessaire...
}
