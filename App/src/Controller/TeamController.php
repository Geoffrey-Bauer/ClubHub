<?php

namespace App\Controller;

use App\Entity\Team;
use App\Form\TeamType;
use App\Repository\TeamRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TeamController extends AbstractController
{
    #[Route('/team/new', name: 'team_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $team = new Team();
        $form = $this->createForm(TeamType::class, $team);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($team);
            $em->flush();

            return $this->redirectToRoute('team_list');
        }

        return $this->render('gestion/team/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/team/list', name: 'team_list')]
    public function list(TeamRepository $teamRepository): Response
    {
        // Récupérer toutes les équipes de la base de données
        $teams = $teamRepository->findAllTeams();

        // Rendre la vue avec les équipes
        return $this->render('gestion/team/list.html.twig', [
            'teams' => $teams,
        ]);
    }

    #[Route('/team/edit/{id}', name: 'team_edit')]
    public function edit(Request $request, Team $team, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(TeamType::class, $team);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('team_list');
        }

        return $this->render('gestion/team/edit.html.twig', [
            'form' => $form->createView(),
            'team' => $team,
        ]);
    }

    #[Route('/team/delete/{id}', name: 'team_delete')]
    public function delete(Team $team, EntityManagerInterface $em): RedirectResponse
    {
        $em->remove($team);
        $em->flush();

        $this->addFlash('success', 'L\'équipe a été supprimée avec succès.');

        return $this->redirectToRoute('team_list');
    }

    // Ajoutez d'autres actions si nécessaire...
}
