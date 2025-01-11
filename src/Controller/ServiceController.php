<?php

namespace App\Controller;

use App\Entity\Service;
use App\Repository\ServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/service')]
final class ServiceController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager, 
        private ServiceRepository $repository)
    {
        
    }
    #[Route(name: 'app_service_index', methods: ['GET'])]
    public function index(ServiceRepository $serviceRepository): Response
    {
        return $this->render('service/index.html.twig', [
            'services' => $serviceRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_service_new', methods: ['POST'])]
    public function new(): Response
    {
        $service = new Service();
        $service->setNom('Mala');
        $service->setDescription("La description service");
        $service->setCreatedAt(new \DateTimeImmutable());
        $service->setUpdatedAt(new \DateTimeImmutable());

        $this->manager->persist($service);
        $this->manager->flush();

        return $this->json(
            ['message' => "Service resource created with {$service->getId()} id"],
            Response::HTTP_CREATED,
        );
    }

    #[Route('/{id}', name: 'app_service_show', methods: ['GET'])]
    public function show(int $id): Response
    {
        $service = $this->repository->findOneBy(['id' => $id]);

        if (!$service) {
            throw $this->createNotFoundException("Pas de service trouvé pour cet {$id} id");
        }

        return $this->json(
            ['message' => "le service trouvé : 
            {$service->getNom()}
            {$service->getDescription()}
            pour {$service->getId()} id"
        ]);
    }

    #[Route('/edit/{id}', name: 'app_service_edit', methods: ['PUT'])]
    public function edit(int $id): Response
    {
        $service = $this->repository->findOneBy(['id' => $id]);

        if (!$service) {
            throw $this->createNotFoundException("Pas de service trouvé pour cet {$id} id");
        }

        $service->setNom('Baba');
        $service->setDescription("La description modifié");
        $this->manager->flush();

        return $this->redirectToRoute('app_service_show', ['id' => $service->getId()]);
    }

    #[Route('/{id}', name: 'app_service_delete', methods: ['DELETE'])]
    public function delete(int $id): Response
    {
        $service = $this->repository->findOneBy(['id' => $id]);

        if (!$service) {
            throw $this->createNotFoundException("Pas d'service trouvé pour cet {$id} id");
        }

        $this->manager->remove($service);
        $this->manager->flush();

        return $this->redirectToRoute('app_service_index', [], Response::HTTP_SEE_OTHER);
    }
}
