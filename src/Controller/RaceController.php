<?php

namespace App\Controller;

use App\Entity\Race;
use App\Repository\RaceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/race')]
final class RaceController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager, 
        private RaceRepository $repository)
    {
        
    }
    #[Route(name: 'app_race_index', methods: ['GET'])]
    public function index(RaceRepository $raceRepository): Response
    {
        return $this->render('race/index.html.twig', [
            'races' => $raceRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_race_new', methods: ['POST'])]
    public function new(): Response
    {
        $race = new Race();
        $race->setLabel('Ma babel');
        $race->setCreatedAt(new \DateTimeImmutable());
        $race->setUpdatedAt(new \DateTimeImmutable());

        $this->manager->persist($race);
        $this->manager->flush();

        return $this->json(
            ['message' => "Race resource created with {$race->getId()} id"],
            Response::HTTP_CREATED,
        );
    }

    #[Route('/{id}', name: 'app_race_show', methods: ['GET'])]
    public function show(int $id): Response
    {
        $race = $this->repository->findOneBy(['id' => $id]);

        if (!$race) {
            throw $this->createNotFoundException("Pas d'race trouvé pour cet {$id} id");
        }

        return $this->json(
            ['message' => "la race trouvé : 
            {$race->getLabel()}
            pour {$race->getId()} id"
        ]);
    }

    #[Route('/edit/{id}', name: 'app_race_edit', methods: ['PUT'])]
    public function edit(int $id): Response
    {
        $race = $this->repository->findOneBy(['id' => $id]);

        if (!$race) {
            throw $this->createNotFoundException("Pas de race trouvé pour cet {$id} id");
        }

        $race->setLabel('Nos babel modifié');
        $this->manager->flush();

        return $this->redirectToRoute('app_race_show', ['id' => $race->getId()]);
    }

    #[Route('/{id}', name: 'app_race_delete', methods: ['DELETE'])]
    public function delete(int $id): Response
    {
        $race = $this->repository->findOneBy(['id' => $id]);

        if (!$race) {
            throw $this->createNotFoundException("Pas d'race trouvé pour cet {$id} id");
        }

        $this->manager->remove($race);
        $this->manager->flush();

        return $this->redirectToRoute('app_race_index', [], Response::HTTP_SEE_OTHER);
    }
}
