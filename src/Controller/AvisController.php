<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Repository\AvisRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Attributes as OA;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('api/avis', name: 'app_api_avis_')]
final class AvisController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private AvisRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator
    ) {}


    #[Route(name: 'new', methods: 'POST')]
    #[OA\Post(
        path: "/api/avis",
        summary: "Créer un avis",
        requestBody: new OA\RequestBody(
            required: true,
            description: "Données de l'avis à créer",
            content: new OA\MediaType(
                mediaType: "application/json",
                schema: new OA\Schema(
                    type: "object",
                    required: ["pseudo", "comments"],
                    properties: [
                        new OA\Property(
                            property: "pseudo",
                            type: "string",
                            example: "kolo"
                        ),
                        new OA\Property(
                            property: "comments",
                            type: "string",
                            example: "Super endroit !"
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
                                property: "pseudo",
                                type: "string",
                                example: "kolo"
                            ),
                            new OA\Property(
                                property: "comments",
                                type: "string",
                                example: "Super endroit !"
                            ),
                            new OA\Property(
                                property: "visible",
                                type: "boolean",
                                example: "false"
                            ),
                            new OA\Property(
                                property: "animal",
                                type: "string",
                                example: "Sama"
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
        $avis = $this->serializer->deserialize(
            $request->getContent(),
            Avis::class,
            'json'
        );

        $avis->setCreatedAt(new DateTimeImmutable());

        // Définit une valeur par défaut pour `isVisible`
        $avis->setVisible(false);

        $this->manager->persist($avis);
        $this->manager->flush();

        $responseData = $this->serializer->serialize($avis, 'json');
        $location = $this->urlGenerator->generate(
            'app_api_avis_show',
            ['id' => $avis->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );

        return new JsonResponse(
            $responseData,
            Response::HTTP_CREATED,
            ["location" => $location],
            true
        );
    }


    #[Route('/employee/validate-avis/{avisId}', name: 'employee_validate_avis', methods: 'PUT')]
    #[IsGranted('ROLE_EMPLOYE')]
    #[OA\Put(
        path: "/api/avis/employee/validate-avis/{avisId}",
        summary: "Valider un avis de visiteur",
        description: "Cette route permet à un employé ou un administrateur de valider un avis de visiteur"
    )]
    #[OA\Parameter(
        name: 'avisId',
        in: 'path',
        required: true,
        description: "Identifiant de l'avis à valider",
        schema: new OA\Schema(
            type: 'integer',
            example: 42
        )
    )]
    #[OA\Response(
        response: 200,
        description: "Avis validé avec succès",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'message',
                    type: 'string',
                    example: "Avis validé avec succès"
                )
            ]
        )
    )]
    #[OA\Response(
        response: 403,
        description: "Accès refusé",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'message',
                    type: 'string',
                    example: 'Accès réfusé'
                )
            ]
        )
    )]
    #[OA\Response(
        response: 404,
        description: "Avis non trouvé",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'error',
                    type: 'string',
                    example: "Avis non trouvé"
                )
            ]
        )
    )]
    public function validateAvis(
        int $avisId,
        EntityManagerInterface $manager
    ): JsonResponse {
        $avis = $manager->getRepository(Avis::class)->find($avisId);

        // Vérification si l'utilisateur a l'un des rôles requis
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_EMPLOYE')) {
            return new JsonResponse(
                ['message' => 'Accès réfusé'],
                Response::HTTP_FORBIDDEN
            );
        }

        if (!$avis) {
            return new JsonResponse(
                ['error' => 'Avis non trouvé'],
                Response::HTTP_NOT_FOUND
            );
        }

        // Valider l'avis du visiteur
        $avis->setVisible(true);
        $manager->flush();

        return new JsonResponse(
            ['message' => 'Avis validé avec succès']
        );
    }

    #[Route('/{id}', name: 'show', methods: 'GET')]
    #[IsGranted('ROLE_EMPLOYE')]
    #[OA\Get(
        path: "/api/avis/{id}",
        summary: "Afficher un avis par son ID",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID de l'avis à afficher",
                schema: new OA\Schema(
                    type: "integer"
                )
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Avis trouvé avec succès",
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
                                property: "pseudo",
                                type: "string",
                                example: "kolo"
                            ),
                            new OA\Property(
                                property: "comments",
                                type: "string",
                                example: "Super endroit !"
                            ),
                            new OA\Property(
                                property: "visible",
                                type: "boolean",
                                example: "true"
                            ),
                            new OA\Property(
                                property: "animal",
                                type: "string",
                                example: "Sama"
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
                description: "Avis non trouvé"
            )
        ]
    )]
    public function show(int $id): JsonResponse
    {
        $avis = $this->repository->findOneBy(['id' => $id]);

        if ($avis) {
            $responseData = $this->serializer->serialize($avis, 'json');

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
        path: "/api/avis/",
        summary: "Récupérer la liste des avis visibles",
        description: "Cette route retourne tous les avis publics visibles."
    )]
    #[OA\Response(
        response: 200,
        description: "Liste des avis visibles",
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(
                properties: [
                    new OA\Property(
                        property: 'pseudo',
                        type: 'string',
                        example: "JohnDoe"
                    ),
                    new OA\Property(
                        property: 'commentaire',
                        type: 'string',
                        example: "Super visite !"
                    ),
                    new OA\Property(
                        property: 'createdAt',
                        type: 'string',
                        example: "12-02-2024"
                    ),
                ]
            )
        )
    )]
    public function index(): JsonResponse
    {
        $avisList = $this->repository->findBy(
            ['isVisible' => true]
        );

        $data = array_map(
            function (Avis $avis) {
                return [
                    'pseudo' => $avis->getPseudo(),
                    'commentaire' => $avis->getComments(),
                    'createdAt' => $avis->getCreatedAt()->format("d-m-Y"),
                ];
            },
            $avisList
        );

        return new JsonResponse(
            $data,
            JsonResponse::HTTP_OK
        );
    }

    #[Route('/{id}', name: 'delete', methods: 'DELETE')]
    #[OA\Delete(
        path: "/api/avis/{id}",
        summary: "Suppression de l'avis",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID du avis à supprimer",
                schema: new OA\Schema(
                    type: "integer"
                )
            )
        ],
        responses: [
            new OA\Response(
                response: 204,
                description: "Avis supprimer avec succès",
            ),
            new OA\Response(
                response: 404,
                description: "Avis non trouvé"
            )
        ]
    )]
    public function delete(int $id): JsonResponse
    {
        $avis = $this->repository->findOneBy(['id' => $id]);

        // Vérification si l'utilisateur a l'un des rôles requis
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_EMPLOYE')) {
            return new JsonResponse(
                ['message' => 'Accès réfusé'],
                Response::HTTP_FORBIDDEN
            );
        }

        if ($avis) {
            $this->manager->remove($avis);
            $this->manager->flush();

            return new JsonResponse(
                ['message' => 'Avis supprimé avec succès'],
                Response::HTTP_OK
            );
        }

        return new JsonResponse(
            null,
            Response::HTTP_NOT_FOUND
        );
    }
}
