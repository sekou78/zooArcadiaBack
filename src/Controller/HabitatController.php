<?php

namespace App\Controller;

use App\Entity\Habitat;
use App\Repository\HabitatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/habitat')]
final class HabitatController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager, 
        private HabitatRepository $repository)
    {
        
    }
    #[Route(name: 'app_habitat_index', methods: ['GET'])]
    public function index(HabitatRepository $habitatRepository): Response
    {
        return $this->render('habitat/index.html.twig', [
            'habitats' => $habitatRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_habitat_new', methods: ['POST'])]
    public function new(): Response
    {
        $habitat = new Habitat();
        $habitat->setName('Toto');
        $habitat->setDescription("La description");
        $habitat->setCommentHabitat("Commentaire habitat");
        $habitat->setCreatedAt(new \DateTimeImmutable());
        $habitat->setUpdatedAt(new \DateTimeImmutable());

        $this->manager->persist($habitat);
        $this->manager->flush();

        return $this->json(
            ['message' => "Habitat resource created with {$habitat->getId()} id"],
            Response::HTTP_CREATED,
        );
    }

    #[Route('/{id}', name: 'app_habitat_show', methods: ['GET'])]
    public function show(int $id): Response
    {
        $habitat = $this->repository->findOneBy(['id' => $id]);

        if (!$habitat) {
            throw $this->createNotFoundException("Pas d'habitat trouvé pour cet {$id} id");
        }

        return $this->json(
            ['message' => "l'habitat trouvé : 
            {$habitat->getName()}
            {$habitat->getDescription()}
            {$habitat->getCommentHabitat()}
            pour {$habitat->getId()} id"
        ]);
    }

    #[Route('/edit/{id}', name: 'app_habitat_edit', methods: ['PUT'])]
    public function edit(int $id): Response
    {
        $habitat = $this->repository->findOneBy(['id' => $id]);

        if (!$habitat) {
            throw $this->createNotFoundException("Pas d'habitat trouvé pour cet {$id} id");
        }

        $habitat->setName('Loute');
        $habitat->setDescription("La description modifié");
        $habitat->setCommentHabitat("Commentaire habitat modifié");
        $this->manager->flush();

        return $this->redirectToRoute('app_habitat_show', ['id' => $habitat->getId()]);
    }

    #[Route('/{id}', name: 'app_habitat_delete', methods: ['DELETE'])]
    public function delete(int $id): Response
    {
        $habitat = $this->repository->findOneBy(['id' => $id]);

        if (!$habitat) {
            throw $this->createNotFoundException("Pas d'habitat trouvé pour cet {$id} id");
        }

        $this->manager->remove($habitat);
        $this->manager->flush();

        return $this->redirectToRoute('app_habitat_index', [], Response::HTTP_SEE_OTHER);
    }
}
