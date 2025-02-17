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
        path: '/api/services',
        summary: 'Créer un nouveau service',
        requestBody: new OA\RequestBody(
            required: true,
            description: "Données de service à créer",
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
                response: 400,
                description: 'Données invalides'
            )
        ]
    )]
    public function new(
        Request $request,
        ValidatorInterface $validator,
    ): JsonResponse {
        $service = $this->serializer->deserialize(
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

        $responseData = $this->serializer->serialize(
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
        summary: "Afficher un service",
        description: "Afficher les détails d'un service via son ID",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID du service",
                schema: new OA\Schema(
                    type: "integer"
                )
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Détails du service",
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
                description: "Rapport vétérinaire non trouvé"
            )
        ]
    )]
    public function show(int $id): JsonResponse
    {
        $service = $this->repository->findOneBy(['id' => $id]);

        if ($service) {
            $responseData = $this->serializer->serialize(
                $service,
                'json',
                ['groups' => 'service_user_read']
            );

            return new JsonResponse(
                $responseData,
                Response::HTTP_OK,
                [],
                true
            );
        }

        return new JsonResponse(
            ['error' => 'Service not found'],
            Response::HTTP_NOT_FOUND
        );
    }

    #[Route('/{id}', name: 'edit', methods: 'PUT')]
    #[OA\Put(
        path: "/api/service/{id}",
        summary: "Mettre à jour un service",
        description: "Mettre à jour un service",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID du service",
                schema: new OA\Schema(
                    type: "integer"
                )
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            description: "Données du service à mettre à jour",
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
                response: 200,
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
                                property: "nom",
                                type: "string",
                                example: "Rangements des produits"
                            ),
                            new OA\Property(
                                property: "description",
                                type: "string",
                                example: "Rangements des produits pour les animaux"
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
                            ),
                            new OA\Property(
                                property: "updatedAt",
                                type: "string",
                                format: "date-time",
                                example: "10-10-2025"
                            )
                        ]
                    )
                )
            ),
            new OA\Response(
                response: 400,
                description: "Données invalides"
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
            $service = $this->serializer->deserialize(
                $request->getContent(),
                Service::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $service]
            );

            $service->setUpdatedAt(new DateTimeImmutable());

            $this->manager->flush();

            $modify = $this->serializer->serialize(
                $service,
                'json',
                ['groups' => 'service_user_read']
            );

            return new JsonResponse(
                $modify,
                Response::HTTP_OK,
                [],
                true
            );
        }

        return new JsonResponse(
            ['error' => 'Service not found'],
            Response::HTTP_NOT_FOUND
        );
    }


    #[Route('/{id}', name: 'delete', methods: 'DELETE')]
    #[OA\Delete(
        path: "/api/service/{id}",
        summary: "Supprimer un service",
        description: "Supprimer un service via son ID.",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID du service",
                schema: new OA\Schema(
                    type: "integer"
                )
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Service supprimé avec succès"
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
                ['message' => 'Service supprimé avec succès'],
                Response::HTTP_OK
            );
        }

        return new JsonResponse(
            ['error' => 'Service not found'],
            Response::HTTP_NOT_FOUND
        );
    }

    #[Route('/api/services', name: 'list', methods: 'GET')]
    public function list(
        Request $request,
        PaginatorInterface $paginator
    ): JsonResponse {
        $queryBuilder = $this->manager->getRepository(
            Service::class
        )->createQueryBuilder('s');

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );

        $items = array_map(function ($service) {
            $utilisateur = $service->getUtilisateurs(); // Récupération de l'utlisateur
            $updatedAt = $service->getUpdatedAt(); // Récupération de updatedAt
            return [
                'id' => $service->getId(),
                'nom' => $service->getNom(),
                'description' => $service->getDescription(),
                'utilisateur' => $utilisateur ? [ // Vérification avant d'accéder aux données de l'utilisateur
                    'username' => $utilisateur->getUsername(),
                ] : null, // Si aucun utilisateur n'est associé, retourne `null`
                'createdAt' => $service->getCreatedAt()->format("d-m-Y"),
                'updatedAt' => $updatedAt ? $updatedAt->format("d-m-Y") : null, // Vérification avant d'ajouter
            ];
        }, (array) $pagination->getItems());

        $data = [
            'currentPage' => $pagination->getCurrentPageNumber(),
            'totalItems' => $pagination->getTotalItemCount(),
            'itemsPerPage' => $pagination->getItemNumberPerPage(),
            'totalPages' => ceil(
                $pagination->getTotalItemCount() / $pagination->getItemNumberPerPage()
            ),
            'items' => $items, // Les éléments paginés formatés
        ];

        return new JsonResponse(
            $data,
            JsonResponse::HTTP_OK
        );
    }
}
