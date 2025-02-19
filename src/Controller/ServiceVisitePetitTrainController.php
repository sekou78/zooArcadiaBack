<?php

namespace App\Controller;

use App\Entity\Service;
use App\Entity\ServiceVisitePetitTrain;
use App\Repository\ServiceVisitePetitTrainRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use OpenApi\Attributes as OA;

#[Route('api/serviceVisitePetitTrain', name: 'app_api_serviceVisitePetitTrain_')]
final class ServiceVisitePetitTrainController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private ServiceVisitePetitTrainRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator
    ) {}

    #[Route(name: 'new', methods: 'POST')]
    #[OA\Post(
        path: "/api/serviceVisitePetitTrain",
        summary: "Créer un Service visite petit train",
        requestBody: new OA\RequestBody(
            required: true,
            description: "Le Service visite petit train a créer",
            content: new OA\MediaType(
                mediaType: "application/json",
                schema: new OA\Schema(
                    type: "object",
                    required: [
                        "parcours",
                        "description",
                        "disponibilite",
                        "duree",
                        "service"
                    ],
                    properties: [
                        new OA\Property(
                            property: "parcours",
                            type: "string",
                            example: "Circuit Jungle"
                        ),
                        new OA\Property(
                            property: "description",
                            type: "string",
                            example: "Un voyage à travers la jungle"
                        ),
                        new OA\Property(
                            property: "disponibilite",
                            type: "array",
                            items: new OA\Items(
                                type: "string"
                            ),
                            example: ["Lundi", "Mercredi", "Vendredi"]
                        ),
                        new OA\Property(
                            property: "duree",
                            type: "string",
                            example: "1h30 min"
                        ),
                        new OA\Property(
                            property: "service",
                            type: "integer",
                            example: 1
                        )
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Parcours créé avec succès",
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
                                property: "parcours",
                                type: "string",
                                example: "Circuit Jungle"
                            ),
                            new OA\Property(
                                property: "description",
                                type: "string",
                                example: "Un voyage à travers la jungle"
                            ),
                            new OA\Property(
                                property: "disponibilite",
                                type: "array",
                                items: new OA\Items(
                                    type: "string"
                                ),
                                example: ["Lundi", "Mercredi", "Vendredi"]
                            ),
                            new OA\Property(
                                property: "duree",
                                type: "string",
                                description: "Durée du parcours",
                                example: "1h30 min"
                            ),
                            new OA\Property(
                                property: "service",
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
                                        example: "Service 1"
                                    ),
                                    new OA\Property(
                                        property: "description",
                                        type: "string",
                                        example: "Description du service 1"
                                    )
                                ]
                            ),
                            new OA\Property(
                                property: "createdAt",
                                type: "string",
                                format: "date-time",
                                example: "18-02-2025 19:49:18"
                            )
                        ]
                    )
                )
            ),
            new OA\Response(
                response: 400,
                description: "Données invalides",
            ),
            new OA\Response(
                response: 403,
                description: "Accès refusé",
            ),
            new OA\Response(
                response: 404,
                description: "Service non trouvé",
            )
        ]
    )]
    public function new(
        Request $request,
        ValidatorInterface $validator
    ): JsonResponse {
        $data = json_decode(
            $request->getContent(),
            true
        );

        // Vérification si l'utilisateur a l'un des rôles requis
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_EMPLOYE')) {
            return new JsonResponse(
                ['message' => 'Accès réfusé'],
                Response::HTTP_FORBIDDEN
            );
        }

        if (!isset($data['service'])) {
            return new JsonResponse(
                ['error' => "L'ID du service est requis"],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        // Récupérer le Service en base
        $service = $this->manager
            ->getRepository(Service::class)
            ->find($data['service']);
        if (!$service) {
            return new JsonResponse(
                ['error' => "Service non trouvé"],
                JsonResponse::HTTP_NOT_FOUND
            );
        }

        // Désérialisation de l'objet ServiceVisitePetitTrain
        $serviceVisitePetitTrain = $this->serializer
            ->deserialize(
                $request->getContent(),
                ServiceVisitePetitTrain::class,
                'json'
            );

        // Associer le Service récupéré
        $serviceVisitePetitTrain->setService($service);

        $serviceVisitePetitTrain->setCreatedAt(new \DateTimeImmutable());

        // Validation
        $errors = $validator->validate($serviceVisitePetitTrain);
        if (count($errors) > 0) {
            return new JsonResponse(
                ['error' => (string) $errors],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $this->manager->persist($serviceVisitePetitTrain);
        $this->manager->flush();

        // Sérialisation en tableau pour modification
        $responseData = json_decode(
            $this->serializer
                ->serialize(
                    $serviceVisitePetitTrain,
                    'json',
                    ['groups' => 'service_visite_petit_train:read']
                ),
            true
        );

        // Ajouter createdAt uniquement s'il n'est pas null
        if ($serviceVisitePetitTrain->getCreatedAt()) {
            $responseData['createdAt'] = $serviceVisitePetitTrain
                ->getCreatedAt()
                ->format('d-m-Y H:i:s');
        }

        // Supprimer updatedAt s'il est null
        if ($serviceVisitePetitTrain->getUpdatedAt()) {
            $responseData['updatedAt'] = $serviceVisitePetitTrain
                ->getUpdatedAt()
                ->format('d-m-Y H:i:s');
        } else {
            unset($responseData['updatedAt']);
        }

        $location = $this->urlGenerator->generate(
            'app_api_serviceVisitePetitTrain_show',
            ['id' => $serviceVisitePetitTrain->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return new JsonResponse(
            $responseData,
            JsonResponse::HTTP_CREATED,
            ["location" => $location]
        );
    }

    #[Route('/{id}', name: 'show', methods: 'GET')]
    #[OA\Get(
        path: "/api/serviceVisitePetitTrain/{id}",
        summary: "Afficher un Service visite petit train par son ID",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID du Service visite petit train",
                schema: new OA\Schema(
                    type: "integer",
                )
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Affichage du Service visite petit train",
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
                                property: "parcours",
                                type: "string",
                                example: "Circuit Jungle"
                            ),
                            new OA\Property(
                                property: "description",
                                type: "string",
                                example: "Un voyage à travers la jungle"
                            ),
                            new OA\Property(
                                property: "disponibilite",
                                type: "array",
                                items: new OA\Items(
                                    type: "string"
                                ),
                                example: ["Lundi", "Mercredi", "Vendredi"]
                            ),
                            new OA\Property(
                                property: "duree",
                                type: "string",
                                example: "1h30 min"
                            ),
                            new OA\Property(
                                property: "service",
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
                                        example: "Service 1"
                                    ),
                                    new OA\Property(
                                        property: "description",
                                        type: "string",
                                        example: "Description du service 1"
                                    )
                                ]
                            ),
                            new OA\Property(
                                property: "createdAt",
                                type: "string",
                                format: "date-time",
                                example: "18-02-2025 19:49:18"
                            )
                        ]
                    )
                )
            ),
            new OA\Response(
                response: 404,
                description: "Service visite petit train non trouvé",
            )
        ]
    )]
    public function show(int $id): JsonResponse
    {
        $serviceVisitePetitTrain = $this->repository->findOneBy(['id' => $id]);

        if (!$serviceVisitePetitTrain) {
            return new JsonResponse(
                ['error' => "Service de visite non trouvé"],
                JsonResponse::HTTP_NOT_FOUND
            );
        }

        // Désérialiser en tableau pour modification
        $responseData = json_decode(
            $this->serializer
                ->serialize(
                    $serviceVisitePetitTrain,
                    'json',
                    ['groups' => 'service_visite_petit_train:read']
                ),
            true
        );

        // Ajouter createdAt uniquement s'il n'est pas null
        if ($serviceVisitePetitTrain->getCreatedAt()) {
            $responseData['createdAt'] = $serviceVisitePetitTrain
                ->getCreatedAt()
                ->format('d-m-Y H:i:s');
        }

        // Supprimer updatedAt s'il est null
        if ($serviceVisitePetitTrain->getUpdatedAt()) {
            $responseData['updatedAt'] = $serviceVisitePetitTrain
                ->getUpdatedAt()
                ->format('d-m-Y H:i:s');
        } else {
            unset(
                $responseData['updatedAt']
            );
        }

        return new JsonResponse(
            $responseData,
            JsonResponse::HTTP_OK
        );
    }


    #[Route('/{id}', name: 'edit', methods: 'PUT')]
    #[OA\Put(
        path: "/api/serviceVisitePetitTrain/{id}",
        summary: "Modifier un Service visite petit train",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID du Service visite petit train",
                schema: new OA\Schema(
                    type: "integer",
                )
            )
        ],
        requestBody: new OA\RequestBody(
            content: new OA\MediaType(
                mediaType: "application/json",
                schema: new OA\Schema(
                    type: "object",
                    properties: [
                        new OA\Property(
                            property: "parcours",
                            type: "string",
                            example: "Circuit Jungle modifié"
                        ),
                        new OA\Property(
                            property: "description",
                            type: "string",
                            example: "Un voyage à travers la jungle modifié"
                        ),
                        new OA\Property(
                            property: "disponibilite",
                            type: "array",
                            items: new OA\Items(
                                type: "string"
                            ),
                            example: ["Lundi", "Jeudi"]
                        ),
                        new OA\Property(
                            property: "duree",
                            type: "string",
                            example: "2h45 min"
                        ),
                        new OA\Property(
                            property: "service",
                            type: "integer",
                            example: 3
                        )
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Service restaurant créer avec succès",
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
                                property: "parcours",
                                type: "string",
                                example: "Circuit Jungle modifié"
                            ),
                            new OA\Property(
                                property: "description",
                                type: "string",
                                example: "Un voyage à travers la jungle modifié"
                            ),
                            new OA\Property(
                                property: "disponibilite",
                                type: "array",
                                items: new OA\Items(
                                    type: "string"
                                ),
                                example: ["Lundi", "Mercredi", "Vendredi"]
                            ),
                            new OA\Property(
                                property: "duree",
                                type: "string",
                                example: "2h45 min"
                            ),
                            new OA\Property(
                                property: "service",
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
                                        example: "Service 3"
                                    ),
                                    new OA\Property(
                                        property: "description",
                                        type: "string",
                                        example: "Description du service 3"
                                    )
                                ]
                            ),
                            new OA\Property(
                                property: "updatedAt",
                                type: "string",
                                format: "date-time",
                                example: "18-02-2025 19:49:18"
                            )
                        ]
                    )
                )
            ),
            new OA\Response(
                response: 404,
                description: "Service restaurant non trouvé"
            )
        ]
    )]
    public function edit(
        int $id,
        Request $request,
        ValidatorInterface $validator
    ): JsonResponse {
        $serviceVisitePetitTrain = $this->repository->findOneBy(['id' => $id]);

        // Vérification si l'utilisateur a l'un des rôles requis
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_EMPLOYE')) {
            return new JsonResponse(
                ['message' => 'Accès réfusé'],
                Response::HTTP_FORBIDDEN
            );
        }

        if (!$serviceVisitePetitTrain) {
            return new JsonResponse(
                ['error' => "Service de visite non trouvé"],
                JsonResponse::HTTP_NOT_FOUND
            );
        }

        $data = json_decode(
            $request->getContent(),
            true
        );

        if (!isset($data['service'])) {
            return new JsonResponse(
                ['error' => "Le champ 'service' est obligatoire"],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $service = $this->manager
            ->getRepository(Service::class)
            ->find($data['service']);
        if (!$service) {
            return new JsonResponse(
                ['error' => "Service non trouvé"],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        try {
            $this->serializer
                ->deserialize(
                    $request->getContent(),
                    ServiceVisitePetitTrain::class,
                    'json',
                    [
                        AbstractNormalizer::OBJECT_TO_POPULATE
                        => $serviceVisitePetitTrain
                    ]
                );
        } catch (\Exception $e) {
            return new JsonResponse(
                [
                    'error' =>
                    "Données invalides : " . $e->getMessage()
                ],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $serviceVisitePetitTrain->setService($service);

        $errors = $validator->validate($serviceVisitePetitTrain);
        if (count($errors) > 0) {
            return new JsonResponse(
                ['error' => (string) $errors],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $serviceVisitePetitTrain->setUpdatedAt(new DateTimeImmutable());

        $this->manager->flush();

        // Désérialiser en tableau pour modification
        $responseData = json_decode(
            $this->serializer
                ->serialize(
                    $serviceVisitePetitTrain,
                    'json',
                    ['groups' => 'service_visite_petit_train:read']
                ),
            true
        );

        // Ajouter createdAt uniquement s'il n'est pas null
        if ($serviceVisitePetitTrain->getCreatedAt()) {
            $responseData['createdAt'] = $serviceVisitePetitTrain
                ->getCreatedAt()
                ->format('d-m-Y H:i:s');
        }

        // Supprimer updatedAt s'il est null
        if ($serviceVisitePetitTrain->getUpdatedAt()) {
            $responseData['updatedAt'] = $serviceVisitePetitTrain
                ->getUpdatedAt()
                ->format('d-m-Y H:i:s');
        } else {
            unset(
                $responseData['updatedAt']
            );
        }

        return new JsonResponse(
            $responseData,
            JsonResponse::HTTP_OK
        );
    }

    #[Route('/{id}', name: 'delete', methods: 'DELETE')]
    #[OA\Delete(
        path: "/api/serviceVisitePetitTrain/{id}",
        summary: "Supprimer un Service visite petit train",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID du Service visite petit train",
                schema: new OA\Schema(
                    type: "integer",
                )
            )
        ],
        responses: [
            new OA\Response(
                response: 204,
                description: "Service visite petit train supprimé avec succès",
            ),
            new OA\Response(
                response: 404,
                description: "Service visite petit train non trouvé",
            )
        ]
    )]
    public function delete(int $id): JsonResponse
    {
        $serviceVisitePetitTrain = $this->repository->findOneBy(['id' => $id]);

        // Vérification si l'utilisateur a l'un des rôles requis
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_EMPLOYE')) {
            return new JsonResponse(
                ['message' => 'Accès réfusé'],
                Response::HTTP_FORBIDDEN
            );
        }

        if ($serviceVisitePetitTrain) {
            $this->manager->remove($serviceVisitePetitTrain);
            $this->manager->flush();

            return new JsonResponse(
                [
                    'message' => "Service de visite en Petit Train à été supprimer avec succès"
                ],
                Response::HTTP_OK
            );
        }

        return new JsonResponse(
            ['error' => 'Service de visite en Petit Train non trouvé'],
            Response::HTTP_NOT_FOUND
        );
    }
}
