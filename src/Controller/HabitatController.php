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
        $habitat = $this->serializer->deserialize(
            $request->getContent(),
            Habitat::class,
            'json'
        );

        // Vérification si l'utilisateur a l'un des rôles requis
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_VETERINAIRE')) {
            return new JsonResponse(
                ['message' => 'Accès réfusé'],
                Response::HTTP_FORBIDDEN
            );
        }

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

    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $habitats = $this->repository->findAll();

        $data = array_map(
            function (Habitat $habitat) {
                return [
                    'name' => $habitat->getName(),
                    'description' => $habitat->getDescription(),
                    'commentHabitat' => $habitat->getCommentHabitat(),
                    'animals' => $habitat->getAnimals(),
                    'images' => $habitat->getImages(),
                ];
            },
            $habitats
        );

        return new JsonResponse(
            $data,
            JsonResponse::HTTP_OK
        );
    }

    #[Route('/{id}', name: 'edit', methods: 'PUT')]
    public function edit(int $id, Request $request): JsonResponse
    {
        $habitat = $this->repository->findOneBy(['id' => $id]);

        // Vérification si l'utilisateur a l'un des rôles requis
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_VETERINAIRE')) {
            return new JsonResponse(
                ['message' => 'Accès réfusé'],
                Response::HTTP_FORBIDDEN
            );
        }

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

        // Vérification si l'utilisateur a l'un des rôles requis
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_VETERINAIRE')) {
            return new JsonResponse(
                ['message' => 'Accès réfusé'],
                Response::HTTP_FORBIDDEN
            );
        }

        if ($habitat) {
            $this->manager->remove($habitat);
            $this->manager->flush();

            return new JsonResponse(
                ['message' => 'Habitat supprimé avec succès'],
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
    public function list(
        Request $request,
        PaginatorInterface $paginator
    ): JsonResponse {
        // Récupérer les paramètres de filtre
        $nameFilter = $request->query->get('name');
        $descriptionFilter = $request->query->get('description');
        $commentHabitatFilter = $request->query->get('commentHabitat');
        $animalsFilter = $request->query->get('animals');
        $imagesFilter = $request->query->get('images');

        // Création de la requête pour récupérer tous les animaux
        $queryBuilder = $this->manager->getRepository(Habitat::class)->createQueryBuilder('a');

        // Appliquer le filtre sur 'name' si le paramètre est présent
        if ($nameFilter) {
            $queryBuilder->andWhere('a.name LIKE :name')
                ->setParameter('name', '%' . $nameFilter . '%');
        }

        // Appliquer le filtre sur 'description' si le paramètre est présent
        if ($descriptionFilter) {
            $queryBuilder->andWhere('a.description LIKE :description')
                ->setParameter('description', '%' . $descriptionFilter . '%');
        }

        // Appliquer le filtre sur 'commentaire habitat' si le paramètre est présent
        if ($commentHabitatFilter) {
            $queryBuilder->andWhere('a.commentHabitat LIKE :commentHabitat')
                ->setParameter('commentHabitat', '%' . $commentHabitatFilter . '%');
        }

        // Appliquer le filtre sur 'animals' si le paramètre est présent
        if ($animalsFilter) {
            $queryBuilder->andWhere('a.animals LIKE :animals')
                ->setParameter('animals', '%' . $animalsFilter . '%');
        }

        // Appliquer le filtre sur 'images' si le paramètre est présent
        if ($imagesFilter) {
            $queryBuilder->andWhere('a.images LIKE :images')
                ->setParameter('images', '%' . $imagesFilter . '%');
        }
        // Pagination avec le paginator
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1), // Page actuelle (par défaut 1)
            5 // Nombre d'éléments par page
        );

        // Formater les résultats
        $items = array_map(function ($animal) {
            return [
                'id' => $animal->getId(),
                'name' => $animal->getName(),
                'description' => $animal->getDescription(),
                'commentaire habitat' => $animal->getCommentHabitat(),
                'animals' => $animal->getAnimals(),
                'images' => $animal->getImages(),
                'createdAt' => $animal->getCreatedAt()->format("d-m-Y"),
            ];
        }, (array) $pagination->getItems());

        // Structure de réponse avec les informations de pagination
        $data = [
            'currentPage' => $pagination->getCurrentPageNumber(),
            'totalItems' => $pagination->getTotalItemCount(),
            'itemsPerPage' => $pagination->getItemNumberPerPage(),
            'totalPages' => ceil($pagination->getTotalItemCount() / $pagination->getItemNumberPerPage()),
            'items' => $items, // Les éléments paginés formatés
        ];

        // Retourner la réponse JSON avec les données paginées
        return new JsonResponse($data, JsonResponse::HTTP_OK);
    }
}
