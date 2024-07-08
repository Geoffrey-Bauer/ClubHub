<?php

namespace App\Controller;

use App\Entity\Coach;
use App\Form\CoachType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CoachController extends AbstractController
{
    #[Route('/coach/new', name: 'new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $coach = new Coach();
        $form = $this->createForm(CoachType::class, $coach);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($coach);
            $em->flush();

            return $this->redirectToRoute('coach_list');
        }

        return $this->render('coach/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // Ajoutez d'autres actions si nécessaire...
}
