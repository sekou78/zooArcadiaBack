<?php

namespace App\Controller;

use App\Entity\RapportVeterinaire;
use App\Form\RapportVeterinaireType;
use App\Repository\RapportVeterinaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/rapport/veterinaire')]
final class RapportVeterinaireController extends AbstractController
{
    #[Route(name: 'app_rapport_veterinaire_index', methods: ['GET'])]
    public function index(RapportVeterinaireRepository $rapportVeterinaireRepository): Response
    {
        return $this->render('rapport_veterinaire/index.html.twig', [
            'rapport_veterinaires' => $rapportVeterinaireRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_rapport_veterinaire_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $rapportVeterinaire = new RapportVeterinaire();
        $form = $this->createForm(RapportVeterinaireType::class, $rapportVeterinaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($rapportVeterinaire);
            $entityManager->flush();

            return $this->redirectToRoute('app_rapport_veterinaire_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('rapport_veterinaire/new.html.twig', [
            'rapport_veterinaire' => $rapportVeterinaire,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_rapport_veterinaire_show', methods: ['GET'])]
    public function show(RapportVeterinaire $rapportVeterinaire): Response
    {
        return $this->render('rapport_veterinaire/show.html.twig', [
            'rapport_veterinaire' => $rapportVeterinaire,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_rapport_veterinaire_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, RapportVeterinaire $rapportVeterinaire, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RapportVeterinaireType::class, $rapportVeterinaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_rapport_veterinaire_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('rapport_veterinaire/edit.html.twig', [
            'rapport_veterinaire' => $rapportVeterinaire,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_rapport_veterinaire_delete', methods: ['POST'])]
    public function delete(Request $request, RapportVeterinaire $rapportVeterinaire, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$rapportVeterinaire->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($rapportVeterinaire);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_rapport_veterinaire_index', [], Response::HTTP_SEE_OTHER);
    }
}
