<?php

namespace App\Controller;

use App\Entity\RapportVeterinaire;
use App\Repository\RapportVeterinaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/rapport_veterinaire')]
final class RapportVeterinaireController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager, 
        private RapportVeterinaireRepository $repository)
    {
        
    }
    #[Route(name: 'app_rapport_veterinaire_index', methods: ['GET'])]
    public function index(RapportVeterinaireRepository $rapport_veterinaireRepository): Response
    {
        return $this->render('rapport_veterinaire/index.html.twig', [
            'rapport_veterinaires' => $rapport_veterinaireRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_rapport_veterinaire_new', methods: ['POST'])]
    public function new(): Response
    {
        $rapport_veterinaire = new RapportVeterinaire();
        $rapport_veterinaire->setDate(new \DateTimeImmutable());
        $rapport_veterinaire->setDetail("Le bon detail");
        $rapport_veterinaire->setCreatedAt(new \DateTimeImmutable());
        $rapport_veterinaire->setUpdatedAt(new \DateTimeImmutable());

        $this->manager->persist($rapport_veterinaire);
        $this->manager->flush();

        return $this->json(
            ['message' => "RapportVeterinaire resource created with {$rapport_veterinaire->getId()} id"],
            Response::HTTP_CREATED,
        );
    }

    #[Route('/{id}', name: 'app_rapport_veterinaire_show', methods: ['GET'])]
    public function show(int $id): Response
    {
        $rapport_veterinaire = $this->repository->findOneBy(['id' => $id]);

        if (!$rapport_veterinaire) {
            throw $this->createNotFoundException("Pas de rapport_veterinaire trouvé pour cet {$id} id");
        }

        return $this->json(
            ['message' => "le rapport_veterinaire trouvé : 
            {$rapport_veterinaire->getDate()->format("d-m-Y")}
            {$rapport_veterinaire->getDetail()}
            pour {$rapport_veterinaire->getId()} id"
        ]);
    }

    #[Route('/edit/{id}', name: 'app_rapport_veterinaire_edit', methods: ['PUT'])]
    public function edit(int $id): Response
    {
        $rapport_veterinaire = $this->repository->findOneBy(['id' => $id]);

        if (!$rapport_veterinaire) {
            throw $this->createNotFoundException("Pas d'rapport_veterinaire trouvé pour cet {$id} id");
        }

        $rapport_veterinaire->setDate(new \DateTimeImmutable(), "modifié");
        $rapport_veterinaire->setDetail("Le detail modifié");
        $this->manager->flush();

        return $this->redirectToRoute('app_rapport_veterinaire_show', ['id' => $rapport_veterinaire->getId()]);
    }

    #[Route('/{id}', name: 'app_rapport_veterinaire_delete', methods: ['DELETE'])]
    public function delete(int $id): Response
    {
        $rapport_veterinaire = $this->repository->findOneBy(['id' => $id]);

        if (!$rapport_veterinaire) {
            throw $this->createNotFoundException("Pas d'rapport_veterinaire trouvé pour cet {$id} id");
        }

        $this->manager->remove($rapport_veterinaire);
        $this->manager->flush();

        return $this->redirectToRoute('app_rapport_veterinaire_index', [], Response::HTTP_SEE_OTHER);
    }
}
