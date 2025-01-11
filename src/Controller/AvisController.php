<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Repository\AvisRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/avis')]
final class AvisController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager, 
        private AvisRepository $repository)
    {
        
    }
    #[Route(name: 'app_avis_index', methods: ['GET'])]
    public function index(AvisRepository $avisRepository): Response
    {
        return $this->render('avis/index.html.twig', [
            'avis' => $avisRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_avis_new', methods: ['POST'])]
    public function new(): Response
    {
        $avis = new Avis();
        $avis->setPseudo('Baba');
        $avis->setComments("Le meilleur des commentaires");
        $avis->setVisible("");
        $avis->setCreatedAt(new \DateTimeImmutable());
        $avis->setUpdatedAt(new \DateTimeImmutable());

        $this->manager->persist($avis);
        $this->manager->flush();

        return $this->json(
            ['message' => "Avis resource created with {$avis->getId()} id"],
            Response::HTTP_CREATED,
        );
    }

    #[Route('/{id}', name: 'app_avis_show', methods: ['GET'])]
    public function show(int $id): Response
    {
        $avis = $this->repository->findOneBy(['id' => $id]);

        if (!$avis) {
            throw $this->createNotFoundException("Pas d'avis trouvé pour cet {$id} id");
        }

        return $this->json(
            ['message' => "l'avis trouvé : {$avis->getPseudo()} pour {$avis->getId()} id"
        ]);
    }

    #[Route('/edit/{id}', name: 'app_avis_edit', methods: ['PUT'])]
    public function edit(int $id): Response
    {
        $avis = $this->repository->findOneBy(['id' => $id]);

        if (!$avis) {
            throw $this->createNotFoundException("Pas d'avis trouvé pour cet {$id} id");
        }

        $avis->setPseudo('Baba');
        $avis->setComments("Commentaires modifié");
        $this->manager->flush();

        return $this->redirectToRoute('app_avis_show', ['id' => $avis->getId()]);
    }

    #[Route('/{id}', name: 'app_avis_delete', methods: ['DELETE'])]
    public function delete(int $id): Response
    {
        $avis = $this->repository->findOneBy(['id' => $id]);

        if (!$avis) {
            throw $this->createNotFoundException("Pas d'avis trouvé pour cet {$id} id");
        }

        $this->manager->remove($avis);
        $this->manager->flush();

        return $this->redirectToRoute('app_avis_index', [], Response::HTTP_SEE_OTHER);
    }
}
