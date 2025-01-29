<?php

namespace App\Controller;

use App\Entity\Animal;
use App\Entity\Habitat;
use App\Entity\RapportVeterinaire;
use App\Entity\Service;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('api/admin', name: 'app_api_admin')]
class AdminController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private SerializerInterface $serializer
    ) {}

    #[Route('/services', name: 'update_service', methods: ['PUT', 'POST', 'DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function manageServices(Request $request): JsonResponse
    {
        // Désérialiser l'entité Service
        $service = $this->serializer->deserialize(
            $request->getContent(),
            Service::class,
            'json'
        );

        $this->manager->persist($service);
        $this->manager->flush();

        return new JsonResponse(
            ['message' => 'Service updated successfully'],
            Response::HTTP_OK
        );
    }

    public function manageHabitats(Request $request): JsonResponse
    {
        // Désérialiser l'entité Service
        $habitat = $this->serializer->deserialize(
            $request->getContent(),
            Habitat::class,
            'json'
        );

        $this->manager->persist($habitat);
        $this->manager->flush();

        return new JsonResponse(
            ['message' => 'Service updated successfully'],
            Response::HTTP_OK
        );
    }

    public function manageAnimals(Request $request): JsonResponse
    {
        // Désérialiser l'entité Service
        $animal = $this->serializer->deserialize(
            $request->getContent(),
            Animal::class,
            'json'
        );

        $this->manager->persist($animal);
        $this->manager->flush();

        return new JsonResponse(
            ['message' => 'Service updated successfully'],
            Response::HTTP_OK
        );
    }

    #[Route('/dashboard/stats', name: 'dashboard_stats', methods: 'GET')]
    #[IsGranted('ROLE_ADMIN')]
    public function getDashboardStats(): JsonResponse
    {
        $stats = $this->manager->getRepository(RapportVeterinaire::class)
            ->createQueryBuilder('r')
            ->select('r.animal, COUNT(r.id) as consultations')
            ->groupBy('r.animal')
            ->getQuery()
            ->getResult();

        return new JsonResponse(
            $stats,
            Response::HTTP_OK
        );
    }
}
