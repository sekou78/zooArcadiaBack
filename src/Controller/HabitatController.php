<?php

namespace App\Controller;

use App\Entity\Habitat;
use App\Repository\HabitatRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('api/habitat', name: 'app_api_habitat_')]
final class HabitatController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private HabitatRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator
    ) {}

    #[Route(name: 'new', methods: 'POST')]
    public function new(Request $request): JsonResponse
    {
        $habitat = $this->serializer->deserialize($request->getContent(), Habitat::class, 'json');
        $habitat->setCreatedAt(new DateTimeImmutable());

        $this->manager->persist($habitat);
        $this->manager->flush();

        $responseData = $this->serializer->serialize($habitat, 'json');
        $location = $this->urlGenerator->generate(
            'app_api_habitat_show',
            ['id' => $habitat->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );

        return new JsonResponse(
            $responseData,
            Response::HTTP_CREATED,
            ["location" => $location],
            true
        );
    }

    #[Route('/{id}', name: 'show', methods: 'GET')]
    public function show(int $id): JsonResponse
    {
        $habitat = $this->repository->findOneBy(['id' => $id]);

        if ($habitat) {
            $responseData = $this->serializer->serialize($habitat, 'json');

            return new JsonResponse(
                $responseData,
                Response::HTTP_OK,
                [],
                true
            );
        }

        return new JsonResponse(
            null,
            Response::HTTP_NOT_FOUND
        );
    }

    #[Route('/{id}', name: 'edit', methods: 'PUT')]
    public function edit(int $id, Request $request): JsonResponse
    {
        $habitat = $this->repository->findOneBy(['id' => $id]);

        if ($habitat) {
            $habitat = $this->serializer->deserialize(
                $request->getContent(),
                Habitat::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $habitat]
            );

            $habitat->setUpdatedAt(new DateTimeImmutable());

            $this->manager->flush();

            $modify = $this->serializer->serialize($habitat, 'json');

            return new JsonResponse(
                $modify,
                Response::HTTP_OK,
                [],
                true
            );
        }

        return new JsonResponse(
            null,
            Response::HTTP_NOT_FOUND
        );
    }


    #[Route('/{id}', name: 'delete', methods: 'DELETE')]
    public function delete(int $id): JsonResponse
    {
        $habitat = $this->repository->findOneBy(['id' => $id]);

        if ($habitat) {
            $this->manager->remove($habitat);
            $this->manager->flush();

            return new JsonResponse(
                ['message' => 'Habitat deleted successfully'],
                Response::HTTP_OK
            );
        }

        return new JsonResponse(
            null,
            Response::HTTP_NOT_FOUND
        );
    }

    // Pagination des habitats
    #[Route('/api/rapports', name: 'list', methods: ['GET'])]
    public function list(Request $request, PaginatorInterface $paginator): JsonResponse
    {
        // Création de la requête pour récupérer tous les habitats
        $queryBuilder = $this->manager->getRepository(Habitat::class)->createQueryBuilder('s');

        // Pagination avec le paginator
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1), // Page actuelle (par défaut 1)
            10 // Nombre d'éléments par page
        );

        // Structure de réponse avec les informations de pagination
        $data = [
            'currentPage' => $pagination->getCurrentPageNumber(),
            'totalItems' => $pagination->getTotalItemCount(),
            'itemsPerPage' => $pagination->getItemNumberPerPage(),
            'totalPages' => ceil($pagination->getTotalItemCount() / $pagination->getItemNumberPerPage()),
            'items' => $pagination->getItems(), // Les éléments paginés
        ];

        // Retourner la réponse JSON avec les données paginées
        return new JsonResponse($data, JsonResponse::HTTP_OK);
    }
}
