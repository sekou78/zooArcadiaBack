<?php

namespace App\Controller;

use App\Entity\Animal;
use App\Repository\AnimalRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/animal')]
final class AnimalController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager, 
        private AnimalRepository $repository)
    {
        
    }
    #[Route(name: 'app_animal_index', methods: ['GET'])]
    public function index(AnimalRepository $animalRepository): Response
    {
        return $this->render('animal/index.html.twig', [
            'animals' => $animalRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_animal_new', methods: ['POST'])]
    public function new(): Response
    {
        $animal = new Animal();
        $animal->setLastname('Toto');
        $animal->setEtat("good");
        $animal->setCreatedAt(new \DateTimeImmutable());
        $animal->setUpdatedAt(new \DateTimeImmutable());

        $this->manager->persist($animal);
        $this->manager->flush();

        return $this->json(
            ['message' => "Animal resource created with {$animal->getId()} id"],
            Response::HTTP_CREATED,
        );
    }

    #[Route('/{id}', name: 'app_animal_show', methods: ['GET'])]
    public function show(int $id): Response
    {
        $animal = $this->repository->findOneBy(['id' => $id]);

        if (!$animal) {
            throw $this->createNotFoundException("Pas d'animal trouvé pour cet {$id} id");
        }

        return $this->json(
            ['message' => "l'animal trouvé : {$animal->getLastname()} pour {$animal->getId()} id"
        ]);
    }

    #[Route('/edit/{id}', name: 'app_animal_edit', methods: ['PUT'])]
    public function edit(int $id): Response
    {
        $animal = $this->repository->findOneBy(['id' => $id]);

        if (!$animal) {
            throw $this->createNotFoundException("Pas d'animal trouvé pour cet {$id} id");
        }

        $animal->setLastname('Toto modifié');
        $animal->setEtat("good modifié");
        $this->manager->flush();

        return $this->redirectToRoute('app_animal_show', ['id' => $animal->getId()]);
    }

    #[Route('/{id}', name: 'app_animal_delete', methods: ['DELETE'])]
    public function delete(int $id): Response
    {
        $animal = $this->repository->findOneBy(['id' => $id]);

        if (!$animal) {
            throw $this->createNotFoundException("Pas d'animal trouvé pour cet {$id} id");
        }

        $this->manager->remove($animal);
        $this->manager->flush();

        return $this->redirectToRoute('app_animal_index', [], Response::HTTP_SEE_OTHER);
    }
}
