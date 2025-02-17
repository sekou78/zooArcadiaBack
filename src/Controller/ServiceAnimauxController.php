<?php

namespace App\Controller;

use App\Entity\ServiceAnimaux;
use App\Repository\ServiceAnimauxRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Attributes as OA;

#[Route('api/serviceAnimaux', name: 'app_api_serviceAnimaux_')]
final class ServiceAnimauxController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private ServiceAnimauxRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator
    ) {}

    #[Route(name: 'new', methods: 'POST')]
    #[IsGranted('ROLE_EMPLOYE')]
    #[OA\Post(
        path: '/api/serviceAnimaux',
        summary: 'Créer un service animal',
        description: 'Créer un nouveau service pour un animal',
        requestBody: new OA\RequestBody(
            content: new OA\MediaType(
                mediaType: 'application/json',
                schema: new OA\Schema(
                    type: 'object',
                    required: ['nom', 'description', 'nourriture', 'quantite'],
                    properties: [
                        new OA\Property(
                            property: 'nom',
                            type: 'string',
                            example: "Service 6"
                        ),
                        new OA\Property(
                            property: 'description',
                            type: 'string',
                            example: "Nourriture des animaux"
                        ),
                        new OA\Property(
                            property: 'nourriture',
                            type: 'string',
                            example: "Carottes"
                        ),
                        new OA\Property(
                            property: 'quantite',
                            type: 'integer',
                            example: 12.5
                        ),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Service animal crée avec succès",
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
                                property: 'nom',
                                type: 'string',
                                example: "Service 6"
                            ),
                            new OA\Property(
                                property: 'description',
                                type: 'string',
                                example: "Nourriture des animaux"
                            ),
                            new OA\Property(
                                property: 'nourriture',
                                type: 'string',
                                example: "Carottes"
                            ),
                            new OA\Property(
                                property: 'quantite',
                                type: 'integer',
                                example: 12.5
                            ),
                            new OA\Property(
                                property: 'date',
                                type: "string",
                                format: "date-time",
                                example: "10-10-2025"
                            ),
                            new OA\Property(
                                property: 'users',
                                type: 'array',
                                items: new OA\Items(
                                    properties: [
                                        new OA\Property(
                                            property: "username",
                                            type: "string",
                                            example: "BOBOLO"
                                        )
                                    ]
                                )
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
                description: "Mauvaise requête"
            ),
            new OA\Response(
                response: 403,
                description: "Accès interdit"
            )
        ]
    )]
    public function new(
        Request $request,
        Security $security,
        CsrfTokenManagerInterface $csrfTokenManager
    ): JsonResponse {
        //Utiliser un jeton CSRF
        // $csrfToken = $request->headers->get('X-CSRF-TOKEN');
        // if (!$csrfTokenManager->isTokenValid(new CsrfToken('registration', $csrfToken))) {
        //     return new JsonResponse(
        //         ['error' => 'Invalid CSRF token'],
        //         Response::HTTP_FORBIDDEN
        //     );
        // }

        // Récupérer l'utilisateur actuellement connecté
        $user = $security->getUser();

        // Vérifier que l'utilisateur est valide
        if (!$user) {
            return new JsonResponse(
                ['error' => 'User not found'],
                Response::HTTP_BAD_REQUEST
            );
        }

        // Récupérer les données envoyées dans la requête
        $data = json_decode($request->getContent(), true);

        // Création du service avec les nouvelles informations
        $serviceAnimal = new ServiceAnimaux();
        $serviceAnimal->setNomAnimal($data['nom'] ?? null);
        $serviceAnimal->setDescription($data['description'] ?? null);
        $serviceAnimal->setNourriture($data['nourriture'] ?? null);
        $serviceAnimal->setQuantite($data['quantite'] ?? null);
        $serviceAnimal->setDateHeure(new \DateTimeImmutable()); // Mettre la date et l'heure actuelle

        $serviceAnimal->setCreatedAt(new \DateTimeImmutable());

        // Associer l'utilisateur à ce service d'animaux
        $serviceAnimal->addUser($user); // Ajouter l'utilisateur au service

        $this->manager->persist($serviceAnimal);
        $this->manager->flush();

        // Sérialiser l'objet ServiceAnimaux et renvoyer une réponse JSON
        $responseData = $this->serializer->serialize(
            $serviceAnimal,
            'json',
            ['groups' => ['service_animaux_read']]
        );

        $location = $this->urlGenerator->generate(
            'app_api_service_show',
            ['id' => $serviceAnimal->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return new JsonResponse(
            // ['message' => 'Service Animal registered successfully']
            //Pour le test à supprimer avant production (mise en ligne)
            $responseData,
            Response::HTTP_CREATED,
            ["location" => $location],
            true
        );
    }

    #[Route('/{id}', name: 'show', methods: 'GET')]
    #[OA\Get(
        path: '/api/serviceAnimaux/{id}',
        summary: 'Obtenir un service par son ID',
        description: 'Service pour un animal via son ID',
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'ID du service animal',
                schema: new OA\Schema(
                    type: 'integer'
                )
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Service pour l'animal trouvé",
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
                                property: 'nom',
                                type: 'string',
                                example: "Service 6"
                            ),
                            new OA\Property(
                                property: 'description',
                                type: 'string',
                                example: "Nourriture des animaux"
                            ),
                            new OA\Property(
                                property: 'nourriture',
                                type: 'string',
                                example: "Carottes"
                            ),
                            new OA\Property(
                                property: 'quantite',
                                type: 'integer',
                                example: 12.5
                            ),
                            new OA\Property(
                                property: 'date',
                                type: "string",
                                format: "date-time",
                                example: "10-10-2025"
                            ),
                            new OA\Property(
                                property: 'users',
                                type: 'array',
                                items: new OA\Items(
                                    properties: [
                                        new OA\Property(
                                            property: "username",
                                            type: "string",
                                            example: "BOBOLO"
                                        )
                                    ]
                                )
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
                description: 'Service non trouvé'
            )
        ]
    )]
    public function show(int $id): JsonResponse
    {
        $serviceAnimal = $this->repository->findOneBy(['id' => $id]);

        if ($serviceAnimal) {
            $responseData = $this->serializer->serialize(
                $serviceAnimal,
                'json',
                ['groups' => ['service_animaux_read', 'user_read']]
            );

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
    #[IsGranted('ROLE_EMPLOYE')]
    #[OA\Put(
        path: '/api/serviceAnimaux/{id}',
        summary: 'Modifier un service pour un animal',
        description: "Mettre à jour les informations d'un service animal",
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'ID du service animal',
                schema: new OA\Schema(
                    type: 'integer'
                )
            )
        ],
        requestBody: new OA\RequestBody(
            content: new OA\MediaType(
                mediaType: 'application/json',
                schema: new OA\Schema(
                    type: 'object',
                    required: ['nom', 'description', 'nourriture', 'quantite'],
                    properties: [
                        new OA\Property(
                            property: "id",
                            type: "integer",
                            example: 1
                        ),
                        new OA\Property(
                            property: 'nom',
                            type: 'string',
                            example: "Service 6 modifié"
                        ),
                        new OA\Property(
                            property: 'description',
                            type: 'string',
                            example: "Mise à jour de la nourriture"
                        ),
                        new OA\Property(
                            property: 'nourriture',
                            type: 'string',
                            example: "Légumes"
                        ),
                        new OA\Property(
                            property: 'quantite',
                            type: 'integer',
                            example: 18.24
                        ),
                        new OA\Property(
                            property: 'date',
                            type: "string",
                            format: "date-time",
                            example: "10-10-2025"
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
        responses: [
            new OA\Response(
                response: 200,
                description: "Service animal mis à jour avec succès",
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
                                property: 'nom',
                                type: 'string',
                                example: "Service 6 modifié"
                            ),
                            new OA\Property(
                                property: 'description',
                                type: 'string',
                                example: "Mise à jour de la nourriture"
                            ),
                            new OA\Property(
                                property: 'nourriture',
                                type: 'string',
                                example: "Légumes"
                            ),
                            new OA\Property(
                                property: 'quantite',
                                type: 'integer',
                                example: 18.24
                            ),
                            new OA\Property(
                                property: 'date',
                                type: "string",
                                format: "date-time",
                                example: "10-10-2025"
                            ),
                            new OA\Property(
                                property: 'users',
                                type: 'array',
                                items: new OA\Items(
                                    properties: [
                                        new OA\Property(
                                            property: "username",
                                            type: "string",
                                            example: "BOBOLO"
                                        )
                                    ]
                                )
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
                response: 404,
                description: 'Service non trouvé'
            ),
            new OA\Response(
                response: 400,
                description: 'Mauvaise requête'
            )
        ]
    )]
    public function edit(
        int $id,
        Request $request,
        Security $security,
    ): JsonResponse {
        // Récupérer l'utilisateur connecté
        $user = $security->getUser();

        // Vérifier si l'utilisateur est valide
        if (!$user) {
            return new JsonResponse(
                ['error' => 'User not found'],
                Response::HTTP_BAD_REQUEST
            );
        }

        // Trouver le service d'animaux existant par son ID
        $serviceAnimal = $this->repository->findOneBy(['id' => $id]);

        if ($serviceAnimal) {
            $data = json_decode($request->getContent(), true);

            // Mise à jour des informations
            $serviceAnimal->setNomAnimal($data['nom'] ?? null);
            $serviceAnimal->setDescription($data['description'] ?? null);
            $serviceAnimal->setNourriture($data['nourriture'] ?? null);
            $serviceAnimal->setQuantite($data['quantite'] ?? null);
            $serviceAnimal->setDateHeure(new \DateTimeImmutable());

            $serviceAnimal->setUpdatedAt(new \DateTimeImmutable());

            $this->manager->flush();

            $modify = $this->serializer->serialize(
                $serviceAnimal,
                'json',
                ['groups' => ['service_animaux_read', 'user_read']]
            );

            return new JsonResponse(
                // ['message' => 'Service Animal modify successfully']
                //Pour le test à supprimer avant production (mise en ligne)
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
}
