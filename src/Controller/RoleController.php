<?php

namespace App\Controller;

use App\Entity\Role;
use App\Entity\User;
use App\Repository\RoleRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use OpenApi\Attributes as OA;

#[Route('api/role', name: 'app_api_role_')]
#[IsGranted('ROLE_ADMIN')]
final class RoleController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private RoleRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator
    ) {}

    #[Route(name: 'new', methods: 'POST')]
    #[OA\Post(
        path: "/api/role",
        summary: "Créer un rôle",
        description: "Créer un rôle dans le système",
        requestBody: new OA\RequestBody(
            description: "Les données pour créer un rôle",
            required: true,
            content: new OA\JsonContent(
                required: ["label"],
                properties: [
                    new OA\Property(
                        property: "label",
                        type: "string",
                        example: "ROLE_TESTE"
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Rôle créer avec succès",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "id",
                            type: "integer",
                            example: 1
                        ),
                        new OA\Property(
                            property: "label",
                            type: "string",
                            example: "ROLE_TESTE"
                        ),
                        new OA\Property(
                            property: "createdAt",
                            type: "string",
                            format: "date-time",
                            example: "10-10-2025"
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: "Requête invalide"
            )
        ]
    )]
    public function new(Request $request): JsonResponse
    {
        $role = $this->serializer
            ->deserialize(
                $request->getContent(),
                Role::class,
                'json'
            );

        $role->setCreatedAt(new DateTimeImmutable());

        $this->manager->persist($role);
        $this->manager->flush();

        $responseData = $this->serializer
            ->serialize(
                $role,
                'json'
            );
        $location = $this->urlGenerator
            ->generate(
                'app_api_role_show',
                ['id' => $role->getId()],
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
    #[IsGranted('ROLE_ADMIN')]
    #[OA\Get(
        path: "/api/role/{id}",
        summary: "Afficher un role par son ID",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID du role à afficher",
                schema: new OA\Schema(
                    type: "integer"
                )
            )
        ],
        responses: [
            new OA\Response(
                response: 201,
                description: "Rôle créer avec succès",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "id",
                            type: "integer",
                            example: 1
                        ),
                        new OA\Property(
                            property: "label",
                            type: "string",
                            example: "ROLE_TESTE"
                        ),
                        new OA\Property(
                            property: "createdAt",
                            type: "string",
                            format: "date-time",
                            example: "10-10-2025"
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Rôle non trouvé"
            )
        ],
    )]
    public function show(int $id): JsonResponse
    {
        $role = $this->repository->findOneBy(['id' => $id]);

        if ($role) {
            $responseData = $this->serializer
                ->serialize(
                    $role,
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
            null,
            Response::HTTP_NOT_FOUND
        );
    }

    #[Route("/assign-role", name: "assign_role", methods: "PUT")]
    #[OA\Put(
        path: "/api/role/assign-role",
        summary: "Assigner un rôle à un utilisateur",
        description: "Assigner un rôle à un utilisateur via son email",
        requestBody: new OA\RequestBody(
            description: "Affecter un rôle à un utilisateur.",
            required: true,
            content: new OA\JsonContent(
                required: ["email", "role_id"],
                properties: [
                    new OA\Property(
                        property: "email",
                        type: "string",
                        example: "teste@test.fr"
                    ),
                    new OA\Property(
                        property: "role_id",
                        type: "integer",
                        example: 3
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Rôle assigné à l'utilisateur avec succès",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "message",
                            type: "string",
                            example: "Rôle attribué avec succès"
                        ),
                        new OA\Property(
                            property: "user",
                            type: "object",
                            properties: [
                                new OA\Property(
                                    property: "id",
                                    type: "integer",
                                    example: 3
                                ),
                                new OA\Property(
                                    property: "email",
                                    type: "string",
                                    example: "teste@test.fr"
                                ),
                                new OA\Property(
                                    property: "role",
                                    type: "string",
                                    example: "ROLE_TESTE"
                                )
                            ]
                        ),
                        new OA\Property(
                            property: "role",
                            type: "object",
                            properties: [
                                new OA\Property(
                                    property: "id",
                                    type: "integer",
                                    example: 3
                                ),
                                new OA\Property(
                                    property: "label",
                                    type: "string",
                                    example: "ROLE_TESTE"
                                ),
                                new OA\Property(
                                    property: "users_count",
                                    type: "integer",
                                    example: 1
                                )
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: "Données invalides"
            ),
            new OA\Response(
                response: 404,
                description: "Utilisateur ou rôle non trouvé"
            )
        ]
    )]
    public function assignRoleToUser(
        Request $request,
        EntityManagerInterface $manager,
        Security $security
    ): JsonResponse {
        // Vérifier que l'utilisateur actuel est un administrateur
        if (!$security->isGranted('ROLE_ADMIN')) {
            return new JsonResponse(
                ['message' => 'Accès refusé'],
                JsonResponse::HTTP_FORBIDDEN
            );
        }

        // Récupérer le contenu JSON
        $data = json_decode(
            $request->getContent(),
            true
        );

        if (!$data || !isset(
            $data['email'],
            $data['role_id']
        )) {
            return new JsonResponse(
                [
                    'message' => 'Données invalides. Un email et un role_id sont requis.'
                ],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $email = $data['email'];
        $roleId = $data['role_id'];

        // Trouver l'utilisateur via son email
        $user = $manager->getRepository(User::class)
            ->findOneBy(['email' => $email]);
        $role = $manager->getRepository(Role::class)
            ->find($roleId);

        if (!$user) {
            return new JsonResponse(
                ['message' => 'Utilisateur non trouvé'],
                JsonResponse::HTTP_NOT_FOUND
            );
        }

        if (!$role) {
            return new JsonResponse(
                ['message' => 'Rôle non trouvé'],
                JsonResponse::HTTP_NOT_FOUND
            );
        }

        // Si l'utilisateur tente de créer un administrateur, vérifiez s'il existe déjà un administrateur
        if ($role->getLabel() === 'ROLE_ADMIN') {
            $existingAdmin = $manager
                ->getRepository(User::class)
                ->findOneBy(
                    ['role' => $role]
                );

            // Si un administrateur existe déjà
            if ($existingAdmin) {
                return new JsonResponse(
                    [
                        'error' => 'Un administrateur existe déjà'
                    ],
                    JsonResponse::HTTP_FORBIDDEN
                );
            }
        }

        // Affecter le rôle à l'utilisateur
        $user->setRole($role);

        // Mettre à jour la collection 'users' du rôle
        $role->addUser($user); // Ajoute l'utilisateur à la collection 'users' du rôle

        // Sauvegarder les modifications dans la base de données
        $this->manager->flush();

        return new JsonResponse([
            'message' => "Rôle attribué à l'utilisateur avec succès.",
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'role' => $role->getLabel(),
            ],
            'role' => [
                'id' => $role->getId(),
                'label' => $role->getLabel(),
                'users_count' => count($role->getUsers()), // Nombre d'utilisateurs associés au rôle
            ]
        ], JsonResponse::HTTP_OK);
    }

    //Suppression d'un role en BDD via son label
    #[Route("/delete-role-by-label", name: "delete_role_by_label", methods: "DELETE")]
    #[OA\Delete(
        path: "/api/role/delete-role-by-label",
        summary: "Supprimer un rôle par son label",
        description: "Supprimer un rôle en spécifiant son label",
        requestBody: new OA\RequestBody(
            description: "Les données pour supprimer un rôle",
            required: true,
            content: new OA\JsonContent(
                required: ["email", "label"],
                properties: [
                    new OA\Property(
                        property: "email",
                        type: "string",
                        example: "teste@test.fr"
                    ),
                    new OA\Property(
                        property: "label",
                        type: "string",
                        example: "ROLE_TESTE"
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Supprimer un rôle",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "message",
                            type: "string",
                            example: "Rôle supprimé avec succès"
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: "Label ou Email manquant"
            ),
            new OA\Response(
                response: 404,
                description: "Rôle ou Email non trouvé"
            )
        ]
    )]
    public function deleteRoleByLabel(
        Request $request,
        EntityManagerInterface $manager
    ): JsonResponse {
        // Récupérer le contenu JSON
        $data = json_decode(
            $request->getContent(),
            true
        );

        if (!$data || !isset($data['label'])) {
            return new JsonResponse(
                ['message' => 'Le label du rôle est requis.'],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $label = $data['label'];

        // Trouver le rôle par son label
        $role = $manager->getRepository(Role::class)
            ->findOneBy(['label' => $label]);

        if (!$role) {
            return new JsonResponse(
                ['message' => 'Rôle non trouvé'],
                JsonResponse::HTTP_NOT_FOUND
            );
        }

        // Supprimer le rôle
        $manager->remove($role);
        $manager->flush();

        return new JsonResponse(
            ['message' => 'Rôle supprimé avec succès'],
            JsonResponse::HTTP_OK
        );
    }

    //Suppression du role attribuer à un utilisateur via son email
    #[Route("/delete-role", name: "delete_role_by_email", methods: "DELETE")]
    #[OA\Delete(
        path: "/api/role/delete-role",
        summary: "Supprimer un rôle d'un utilisateur par son email",
        description: "Retirer un rôle d'un utilisateur en spécifiant son email",
        requestBody: new OA\RequestBody(
            description: "Les données pour retirer un rôle d'un utilisateur.",
            required: true,
            content: new OA\JsonContent(
                required: ["email"],
                properties: [
                    new OA\Property(
                        property: "email",
                        type: "string",
                        example: "teste@test.fr"
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Rôle retiré avec succès",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "message",
                            type: "string",
                            example: "Rôle retiré avec succès"
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: "Email manquant"
            ),
            new OA\Response(
                response: 404,
                description: "Utilisateur non trouvé"
            )
        ]
    )]
    public function deleteRoleByEmail(
        Request $request,
        EntityManagerInterface $manager
    ): JsonResponse {
        // Récupérer le contenu JSON
        $data = json_decode(
            $request->getContent(),
            true
        );

        if (!$data || !isset($data['email'])) {
            return new JsonResponse(
                ['message' => 'L\'email est requis.'],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        // Trouver l'utilisateur par email
        $email = $data['email'];
        $user = $manager->getRepository(User::class)
            ->findOneBy(
                ['email' => $email]
            );

        if (!$user) {
            return new JsonResponse(
                ['message' => 'Utilisateur non trouvé'],
                JsonResponse::HTTP_NOT_FOUND
            );
        }

        // Vérifier si l'utilisateur a un rôle attribué
        $role = $user->getRole();
        if (!$role) {
            return new JsonResponse(
                ['message' => 'L\'utilisateur n\'a pas de rôle attribué'],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        // Retirer le rôle de l'utilisateur
        $user->setRole(null);  // Retirer le rôle de l'utilisateur

        // Enregistrer les changements dans la base de données
        $manager->flush();

        return new JsonResponse(
            ['message' => "Rôle retiré avec succès de l'utilisateur"],
            JsonResponse::HTTP_OK
        );
    }
}
