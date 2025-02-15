<?php

namespace App\Controller;

use App\Entity\ServiceVisitePetitTrain;
use App\Form\ServiceVisitePetitTrainType;
use App\Repository\ServiceVisitePetitTrainRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/service/visite/petit/train')]
final class ServiceVisitePetitTrainController extends AbstractController
{
    #[Route(name: 'app_service_visite_petit_train_index', methods: ['GET'])]
    public function index(ServiceVisitePetitTrainRepository $serviceVisitePetitTrainRepository): Response
    {
        return $this->render('service_visite_petit_train/index.html.twig', [
            'service_visite_petit_trains' => $serviceVisitePetitTrainRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_service_visite_petit_train_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $serviceVisitePetitTrain = new ServiceVisitePetitTrain();
        $form = $this->createForm(ServiceVisitePetitTrainType::class, $serviceVisitePetitTrain);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($serviceVisitePetitTrain);
            $entityManager->flush();

            return $this->redirectToRoute('app_service_visite_petit_train_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('service_visite_petit_train/new.html.twig', [
            'service_visite_petit_train' => $serviceVisitePetitTrain,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_service_visite_petit_train_show', methods: ['GET'])]
    public function show(ServiceVisitePetitTrain $serviceVisitePetitTrain): Response
    {
        return $this->render('service_visite_petit_train/show.html.twig', [
            'service_visite_petit_train' => $serviceVisitePetitTrain,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_service_visite_petit_train_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ServiceVisitePetitTrain $serviceVisitePetitTrain, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ServiceVisitePetitTrainType::class, $serviceVisitePetitTrain);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_service_visite_petit_train_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('service_visite_petit_train/edit.html.twig', [
            'service_visite_petit_train' => $serviceVisitePetitTrain,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_service_visite_petit_train_delete', methods: ['POST'])]
    public function delete(Request $request, ServiceVisitePetitTrain $serviceVisitePetitTrain, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$serviceVisitePetitTrain->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($serviceVisitePetitTrain);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_service_visite_petit_train_index', [], Response::HTTP_SEE_OTHER);
    }
}
