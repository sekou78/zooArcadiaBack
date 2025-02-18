<?php

namespace App\Controller;

use App\Entity\Service;
use App\Repository\ServiceRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use OpenApi\Attributes as OA;

#[Route('api/service', name: 'app_api_service_')]
final class ServiceController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private ServiceRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator
    ) {}

    #[Route(name: 'new', methods: 'POST')]
    #[IsGranted('ROLE_ADMIN')]
    #[OA\Post(
        path: "/api/service",
        summary: "Créer un nouveau service",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: "application/json",
                schema: new OA\Schema(
                    type: "object",
                    required: ["nom", "description"],
                    properties: [
                        new OA\Property(
                            property: "nom",
                            type: "string",
                            example: "Nettoyage des cages"
                        ),
                        new OA\Property(
                            property: "description",
                            type: "string",
                            example: "Nettoyage quotidien des cages des animaux"
                        )
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Service créé avec succès",
                content: new OA\MediaType(
                    mediaType: "application/json",
                    schema: new OA\Schema(
                        type: "object",
                        properties: [
                            new OA\Property(
                                property: "id",
                                type: "integer",
                                description: "ID du service"
                            ),
                            new OA\Property(
                                property: "nom",
                                type: "string",
                                example: "Nettoyage des cages"
                            ),
                            new OA\Property(
                                property: "description",
                                type: "string",
                                example: "Nettoyage quotidien des cages des animaux"
                            ),
                            new OA\Property(
                                property: "createdAt",
                                type: "string",
                                format: "date-time",
                                description: "Date de création"
                            ),
                            new OA\Property(
                                property: "user",
                                type: "object",
                                description: "Utilisateur connecté",
                                properties: [
                                    new OA\Property(
                                        property: "username",
                                        type: "string",
                                        example: "Dinga"
                                    )
                                ]
                            )
                        ]
                    )
                )
            ),
            new OA\Response(
                response: 400,
                description: "Erreur de validation",
                content: new OA\MediaType(
                    mediaType: "application/json",
                    schema: new OA\Schema(
                        type: "string",
                        description: "Message d'erreur de validation"
                    )
                )
            )
        ]
    )]
    public function new(
        Request $request,
        ValidatorInterface $validator,
    ): JsonResponse {
        $service = $this->serializer
            ->deserialize(
                $request->getContent(),
                Service::class,
                'json'
            );

        // Validation
        $errors = $validator->validate($service);
        if (count($errors) > 0) {
            return new JsonResponse(
                (string) $errors,
                Response::HTTP_BAD_REQUEST
            );
        }


        // Ajouter l'utilisateur connecté
        $service->addUtilisateur($this->getUser());

        $service->setCreatedAt(new DateTimeImmutable());

        $this->manager->persist($service);
        $this->manager->flush();

        $responseData = $this->serializer
            ->serialize(
                $service,
                'json',
                ['groups' => 'service_user_read']
            );

        $location = $this->urlGenerator->generate(
            'app_api_service_show',
            ['id' => $service->getId()],
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
        path: "/api/service/{id}",
        summary: "Afficher un service par son ID",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID du service à afficher",
                schema: new OA\Schema(
                    type: "integer"
                )
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Service trouvé avec succès",
                content: new OA\MediaType(
                    mediaType: "application/json",
                    schema: new OA\Schema(
                        type: "object",
                        properties: [
                            new OA\Property(
                                property: "id",
                                type: "integer",
                                description: "ID du service"
                            ),
                            new OA\Property(
                                property: "nom",
                                type: "string",
                                example: "Nettoyage des cages"
                            ),
                            new OA\Property(
                                property: "description",
                                type: "string",
                                example: "Nettoyage quotidien des cages des animaux"
                            ),
                            new OA\Property(
                                property: "createdAt",
                                type: "string",
                                format: "date-time",
                                description: "Date de création"
                            ),
                            new OA\Property(
                                property: "user",
                                type: "object",
                                description: "Utilisateur connecté",
                                properties: [
                                    new OA\Property(
                                        property: "username",
                                        type: "string",
                                        example: "Dinga"
                                    )
                                ]
                            )
                        ]
                    )
                )
            ),
            new OA\Response(
                response: 404,
                description: "Service non trouvé"
            )
        ]
    )]
    public function show(int $id): JsonResponse
    {
        $service = $this->repository->findOneBy(['id' => $id]);

        if ($service) {
            $responseData = $this->serializer
                ->serialize(
                    $service,
                    'json'
                );

            return new JsonResponse(
                $responseData,
                Response::HTTP_OK,
                [],
                true
            );
        }

        return new JsonResponse(
            ['error' => 'Service non trouvé'],
            Response::HTTP_NOT_FOUND
        );
    }

    #[Route('/{id}', name: 'edit', methods: 'PUT')]
    #[IsGranted('ROLE_ADMIN')]
    #[OA\Put(
        path: "/api/service/{id}",
        summary: "Mise à jour du service",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID du service à modifier",
                schema: new OA\Schema(
                    type: "integer"
                )
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            description: "Données du service à modifier",
            content: new OA\MediaType(
                mediaType: "application/json",
                schema: new OA\Schema(
                    type: "object",
                    required: ["nom", "description"],
                    properties: [
                        new OA\Property(
                            property: "nom",
                            type: "string",
                            example: "Rangement des produits"
                        ),
                        new OA\Property(
                            property: "etat",
                            type: "string",
                            example: "Rangement des produits pour les animaux"
                        )
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 204,
                description: "Service modifé avec succès",
                content: new OA\MediaType(
                    mediaType: "application/json",
                    schema: new OA\Schema(
                        type: "object",
                        properties: [
                            new OA\Property(
                                property: "id",
                                type: "integer",
                                description: "ID du service"
                            ),
                            new OA\Property(
                                property: "nom",
                                type: "string",
                                example: "Rangement des produits"
                            ),
                            new OA\Property(
                                property: "description",
                                type: "string",
                                example: "Rangement des produits pour les animaux"
                            ),
                            new OA\Property(
                                property: "updatedAt",
                                type: "string",
                                format: "date-time",
                                description: "Date de création"
                            ),
                            new OA\Property(
                                property: "user",
                                type: "object",
                                description: "Utilisateur connecté",
                                properties: [
                                    new OA\Property(
                                        property: "username",
                                        type: "string",
                                        example: "Dinga"
                                    )
                                ]
                            )
                        ]
                    )
                )
            ),
            new OA\Response(
                response: 404,
                description: "Service non trouvé"
            )
        ]
    )]
    public function edit(
        int $id,
        Request $request
    ): JsonResponse {
        $service = $this->repository->findOneBy(['id' => $id]);

        if ($service) {
            $service = $this->serializer
                ->deserialize(
                    $request->getContent(),
                    Service::class,
                    'json',
                    [AbstractNormalizer::OBJECT_TO_POPULATE => $service]
                );

            $service->setUpdatedAt(new DateTimeImmutable());

            $this->manager->flush();

            $modify = $this->serializer
                ->serialize(
                    $service,
                    'json'
                );

            return new JsonResponse(
                $modify,
                Response::HTTP_OK,
                [],
                true
            );
        }

        return new JsonResponse(
            ['error' => 'Service non trouvé'],
            Response::HTTP_NOT_FOUND
        );
    }


    #[Route('/{id}', name: 'delete', methods: 'DELETE')]
    #[IsGranted('ROLE_ADMIN')]
    #[OA\Delete(
        path: "/api/service/{id}",
        summary: "Suppression du service",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID du service à supprimer",
                schema: new OA\Schema(
                    type: "integer"
                )
            )
        ],
        responses: [
            new OA\Response(
                response: 204,
                description: "Service supprimer avec succès",
            ),
            new OA\Response(
                response: 404,
                description: "Service non trouvé"
            )
        ]
    )]
    public function delete(int $id): JsonResponse
    {
        $service = $this->repository->findOneBy(['id' => $id]);

        if ($service) {
            $this->manager->remove($service);
            $this->manager->flush();

            return new JsonResponse(
                ['message' => 'Service supprimer avec succès'],
                Response::HTTP_OK
            );
        }

        return new JsonResponse(
            ['error' => 'Service non trouvé'],
            Response::HTTP_NOT_FOUND
        );
    }

    #[Route('/api/services', name: 'list', methods: ['GET'])]
    #[OA\Get(
        path: '/api/service/api/services',
        summary: "Liste paginée des services avec filtres",
        description: "Liste paginée des services avec filtres"
    )]
    #[OA\Parameter(
        name: 'page',
        in: 'query',
        description: "Numéro de la page (par défaut 1)",
        schema: new OA\Schema(
            type: 'integer',
            default: 1
        )
    )]
    #[OA\Parameter(
        name: 'nom',
        in: 'query',
        description: "Filtrer par nom de service",
        schema: new OA\Schema(
            type: 'string'
        )
    )]
    #[OA\Response(
        response: 200,
        description: "Retourne une liste paginée des services",
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(
                    property: 'currentPage',
                    type: 'integer',
                    example: 1
                ),
                new OA\Property(
                    property: 'totalItems',
                    type: 'integer',
                    example: 50
                ),
                new OA\Property(
                    property: 'itemsPerPage',
                    type: 'integer',
                    example: 5
                ),
                new OA\Property(
                    property: 'totalPages',
                    type: 'integer',
                    example: 10
                ),
                new OA\Property(
                    property: 'items',
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(
                                property: 'id',
                                type: 'integer',
                                example: 1
                            ),
                            new OA\Property(
                                property: 'nom',
                                type: 'string',
                                example: 'Nettoyage des cages'
                            ),
                            new OA\Property(
                                property: 'description',
                                type: 'string',
                                example: 'Nettoyage quotidien des cages des animaux'
                            ),
                            new OA\Property(
                                property: "user",
                                type: "object",
                                description: "Utilisateur connecté",
                                properties: [
                                    new OA\Property(
                                        property: "username",
                                        type: "string",
                                        example: "Dinga"
                                    )
                                ]
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
        // Récupération du filtre 'nom'
        $nomFilter = $request->query->get('nom');

        // Récupération des services avec les utilisateurs associés
        $queryBuilder = $this->manager
            ->getRepository(Service::class)
            ->createQueryBuilder('s')
            ->leftJoin('s.utilisateurs', 'u')
            ->addSelect('u');

        // Appliquer le filtre sur 'nom' si présent
        if ($nomFilter) {
            $queryBuilder->andWhere('s.nom LIKE :nom')
                ->setParameter('nom', '%' . $nomFilter . '%');
        }

        // Pagination
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );

        // Transformation des données pour renvoyer un JSON propre
        $services = [];
        foreach ($pagination->getItems() as $service) {
            $utilisateurs = [];
            foreach ($service->getUtilisateurs() as $utilisateur) {
                $utilisateurs[] = [
                    'username' => $utilisateur->getUsername()
                ];
            }

            $services[] = [
                'id' => $service->getId(),
                'nom' => $service->getNom(),
                'description' => $service->getDescription(),
                'utilisateurs' => $utilisateurs
            ];
        }

        // Structure de réponse JSON
        $data = [
            'currentPage' => $pagination->getCurrentPageNumber(),
            'totalItems' => $pagination->getTotalItemCount(),
            'itemsPerPage' => $pagination->getItemNumberPerPage(),
            'totalPages' => ceil($pagination->getTotalItemCount() / $pagination->getItemNumberPerPage()),
            'items' => $services,
        ];

        return new JsonResponse(
            $data,
            JsonResponse::HTTP_OK
        );
    }
}
