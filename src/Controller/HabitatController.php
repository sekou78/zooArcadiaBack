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
use OpenApi\Attributes as OA;

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
    #[OA\Post(
        path: "/api/habitat",
        summary: "Créer un habitat",
        requestBody: new OA\RequestBody(
            required: true,
            description: "Données de l'habitat à créer",
            content: new OA\MediaType(
                mediaType: "application/json",
                schema: new OA\Schema(
                    type: "object",
                    required: ["name", "description", "CommentHabitat", "image"],
                    properties: [
                        new OA\Property(
                            property: "name",
                            type: "string",
                            example: "Marais"
                        ),
                        new OA\Property(
                            property: "description",
                            type: "string",
                            example: "Un habitat riche"
                        ),
                        new OA\Property(
                            property: "CommentHabitat",
                            type: "string",
                            example: "C'est un environnement humide et froid"
                        ),
                        new OA\Property(
                            property: "image",
                            type: "string",
                            format: "binary",
                            description: "Image de l'habitat (fichier à uploader)"
                        )
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Avis créer avec succès",
                content: new OA\MediaType(
                    mediaType: "application/json",
                    schema: new OA\Schema(
                        type: "object",
                        properties: [
                            new OA\Property(
                                property: "id",
                                type: "integer",
                                example: 1
                            ),
                            new OA\Property(
                                property: "name",
                                type: "string",
                                example: "Marais"
                            ),
                            new OA\Property(
                                property: "description",
                                type: "string",
                                example: "Un habitat riche"
                            ),
                            new OA\Property(
                                property: "CommentHabitat",
                                type: "string",
                                example: "C'est un environnement humide et froid"
                            ),
                            new OA\Property(
                                property: "animal",
                                type: "string",
                                example: "Sama"
                            ),
                            new OA\Property(
                                property: "imageUrl",
                                type: "string",
                                example: "https://example.com/uploads/marais.jpg"
                            ),
                            new OA\Property(
                                property: "createdAt",
                                type: "string",
                                format: "date-time",
                                example: "10-10-2025"
                            )
                        ]
                    )
                )
            )
        ]
    )]
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
    #[OA\Get(
        path: "/api/habitat/{id}",
        summary: "Afficher un habitat par son ID",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID de l'habitat à afficher",
                schema: new OA\Schema(
                    type: "integer"
                )
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Habitat trouvé avec succès",
                content: new OA\MediaType(
                    mediaType: "application/json",
                    schema: new OA\Schema(
                        type: "object",
                        properties: [
                            new OA\Property(
                                property: "id",
                                type: "integer",
                                example: 1
                            ),
                            new OA\Property(
                                property: "name",
                                type: "string",
                                example: "Marais"
                            ),
                            new OA\Property(
                                property: "description",
                                type: "string",
                                example: "Un habitat riche"
                            ),
                            new OA\Property(
                                property: "CommentHabitat",
                                type: "string",
                                example: "C'est un environnement humide et froid"
                            ),
                            new OA\Property(
                                property: "animal",
                                type: "string",
                                example: "Sama"
                            ),
                            new OA\Property(
                                property: "imageUrl",
                                type: "string",
                                example: "https://example.com/uploads/marais.jpg"
                            ),
                            new OA\Property(
                                property: "createdAt",
                                type: "string",
                                format: "date-time",
                                example: "10-10-2025"
                            )
                        ]
                    )
                )
            ),
            new OA\Response(
                response: 404,
                description: "Habitat non trouvé"
            )
        ]
    )]
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
    #[OA\Get(
        path: "/api/habitat/",
        summary: "Récupérer la liste des habitat visibles",
        description: "Cette route retourne tous les habitats"
    )]
    #[OA\Response(
        response: 200,
        description: "Liste des habitat visibles",
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(
                properties: [
                    new OA\Property(
                        property: "name",
                        type: "string",
                        example: "Marais"
                    ),
                    new OA\Property(
                        property: "description",
                        type: "string",
                        example: "Un habitat riche"
                    ),
                    new OA\Property(
                        property: "CommentHabitat",
                        type: "string",
                        example: "C'est un environnement humide et froid"
                    ),
                    new OA\Property(
                        property: "animal",
                        type: "string",
                        example: "Sama"
                    ),
                    new OA\Property(
                        property: "imageUrl",
                        type: "string",
                        example: "https://example.com/uploads/marais.jpg"
                    ),
                    new OA\Property(
                        property: "createdAt",
                        type: "string",
                        format: "date-time",
                        example: "10-10-2025"
                    )
                ]
            )
        )
    )]
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
    #[OA\Put(
        path: "/api/habitat/{id}",
        summary: "Mise à jour de l'habitat",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID de l'habitat à modifier",
                schema: new OA\Schema(
                    type: "integer"
                )
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            description: "Données de l'habitat à modifier",
            content: new OA\MediaType(
                mediaType: "application/json",
                schema: new OA\Schema(
                    type: "object",
                    required: ["firstname", "etat"],
                    properties: [
                        new OA\Property(
                            property: "name",
                            type: "string",
                            example: "Marais"
                        ),
                        new OA\Property(
                            property: "description",
                            type: "string",
                            example: "Un habitat riche"
                        ),
                        new OA\Property(
                            property: "CommentHabitat",
                            type: "string",
                            example: "C'est un environnement humide"
                        ),
                        new OA\Property(
                            property: "image",
                            type: "string",
                            format: "binary",
                            description: "Image de l'habitat (fichier à uploader)"
                        )
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 204,
                description: "Booking modifé avec succès",
                content: new OA\MediaType(
                    mediaType: "application/json",
                    schema: new OA\Schema(
                        type: "object",
                        properties: [
                            new OA\Property(
                                property: "id",
                                type: "integer",
                                example: 1
                            ),
                            new OA\Property(
                                property: "name",
                                type: "string",
                                example: "Marais"
                            ),
                            new OA\Property(
                                property: "description",
                                type: "string",
                                example: "Un habitat riche"
                            ),
                            new OA\Property(
                                property: "CommentHabitat",
                                type: "string",
                                example: "C'est un environnement humide"
                            ),
                            new OA\Property(
                                property: "animal",
                                type: "string",
                                example: "Sama"
                            ),
                            new OA\Property(
                                property: "imageUrl",
                                type: "string",
                                example: "https://example.com/uploads/marais.jpg"
                            ),
                            new OA\Property(
                                property: "updatedAt",
                                type: "string",
                                format: "date-time",
                                example: "10-12-2025"
                            )
                        ]
                    )
                )
            ),
            new OA\Response(
                response: 404,
                description: "Habitat non trouvé"
            )
        ]
    )]
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
    #[OA\Delete(
        path: "/api/habitat/{id}",
        summary: "Suppression de l'habitat",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID de l'habitat à supprimer",
                schema: new OA\Schema(
                    type: "integer"
                )
            )
        ],
        responses: [
            new OA\Response(
                response: 204,
                description: "Habitat supprimer avec succès",
            ),
            new OA\Response(
                response: 404,
                description: "Habitat non trouvé"
            )
        ]
    )]
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
    #[OA\Get(
        path: "/api/habitat/api/rapports",
        summary: "Lister les habitats avec pagination et filtres",
        description: "Cette route permet de récupérer une liste paginée des habitats avec plusieurs filtres disponibles."
    )]
    #[OA\Parameter(
        name: "name",
        in: "query",
        description: "Filtrer par nom d'habitat",
        required: false,
        schema: new OA\Schema(
            type: "string",
            example: "Marais"
        )
    )]
    #[OA\Parameter(
        name: "description",
        in: "query",
        description: "Filtrer par description",
        required: false,
        schema: new OA\Schema(
            type: "string",
            example: "Un habitat riche"
        )
    )]
    #[OA\Parameter(
        name: "commentHabitat",
        in: "query",
        description: "Filtrer par commentaire de l'habitat",
        required: false,
        schema: new OA\Schema(
            type: "string",
            example: "C'est un environnement humide et froid"
        )
    )]
    #[OA\Parameter(
        name: "animals",
        in: "query",
        description: "Filtrer par animaux présents",
        required: false,
        schema: new OA\Schema(
            type: "string",
            example: "Sama"
        )
    )]
    #[OA\Parameter(
        name: "images",
        in: "query",
        description: "Filtrer par images",
        required: false,
        schema: new OA\Schema(
            type: "string",
            example: "image.jpg"
        )
    )]
    #[OA\Parameter(
        name: "page",
        in: "query",
        description: "Numéro de la page pour la pagination (par défaut: 1)",
        required: false,
        schema: new OA\Schema(
            type: "integer",
            example: 1
        )
    )]
    #[OA\Response(
        response: 200,
        description: "Liste paginée des habitats",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: "currentPage",
                    type: "integer",
                    example: 1
                ),
                new OA\Property(
                    property: "totalItems",
                    type: "integer",
                    example: 25
                ),
                new OA\Property(
                    property: "itemsPerPage",
                    type: "integer",
                    example: 5
                ),
                new OA\Property(
                    property: "totalPages",
                    type: "integer",
                    example: 5
                ),
                new OA\Property(
                    property: "items",
                    type: "array",
                    items: new OA\Items(
                        properties: [
                            new OA\Property(
                                property: "id",
                                type: "integer",
                                example: 1
                            ),
                            new OA\Property(
                                property: "name",
                                type: "string",
                                example: "Marais"
                            ),
                            new OA\Property(
                                property: "description",
                                type: "string",
                                example: "Un habitat riche"
                            ),
                            new OA\Property(
                                property: "commentaire habitat",
                                type: "string",
                                example: "C'est un environnement humide et froid"
                            ),
                            new OA\Property(
                                property: "animals",
                                type: "string",
                                example: "Sama"
                            ),
                            new OA\Property(
                                property: "createdAt",
                                type: "string",
                                format: "date",
                                example: "10-10-2025"
                            )
                        ]
                    )
                )
            ]
        )
    )]
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
        $items = array_map(function ($habitat) {
            return [
                'id' => $habitat->getId(),
                'name' => $habitat->getName(),
                'description' => $habitat->getDescription(),
                'commentaire habitat' => $habitat->getCommentHabitat(),
                'animals' => $habitat->getAnimals(),
                'createdAt' => $habitat->getCreatedAt()->format("d-m-Y"),
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
