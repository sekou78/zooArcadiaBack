<?php

namespace App\Controller;

use App\Entity\Race;
use App\Repository\RaceRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Attributes as OA;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('api/race', name: 'app_api_race_')]
#[IsGranted('ROLE_ADMIN')]
final class RaceController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private RaceRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator
    ) {}

    #[Route(name: 'new', methods: 'POST')]
    #[OA\Post(
        path: "/api/race",
        summary: "Créer une race",
        requestBody: new OA\RequestBody(
            required: true,
            description: "Données de la race à créer",
            content: new OA\MediaType(
                mediaType: "application/json",
                schema: new OA\Schema(
                    type: "object",
                    required: ["label"],
                    properties: [
                        new OA\Property(
                            property: "label",
                            type: "string",
                            example: "tigre"
                        )
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Race créer avec succès",
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
                                property: "label",
                                type: "string",
                                example: "tigre"
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
        $race = $this->serializer
            ->deserialize(
                $request->getContent(),
                Race::class,
                '
            json'
            );

        $race->setCreatedAt(new DateTimeImmutable());

        $this->manager->persist($race);
        $this->manager->flush();

        $responseData = $this->serializer
            ->serialize(
                $race,
                'json'
            );
        $location = $this->urlGenerator
            ->generate(
                'app_api_race_show',
                ['id' => $race->getId()],
                UrlGeneratorInterface::ABSOLUTE_URL
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
        path: "/api/race/{id}",
        summary: "Afficher une race par son ID",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID de la race à afficher",
                schema: new OA\Schema(
                    type: "integer"
                )
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Race trouvé avec succès",
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
                                property: "label",
                                type: "string",
                                example: "tigre"
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
                description: "Race non trouvé"
            )
        ]
    )]
    public function show(int $id): JsonResponse
    {
        $race = $this->repository->findOneBy(['id' => $id]);

        if ($race) {
            $responseData = $this->serializer
                ->serialize(
                    $race,
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

    #[Route('/{id}', name: 'edit', methods: 'PUT')]
    #[OA\Put(
        path: "/api/race/{id}",
        summary: "Mise à jour de la race",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID de la race à modifier",
                schema: new OA\Schema(
                    type: "integer"
                )
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            description: "Données de la race à modifier",
            content: new OA\MediaType(
                mediaType: "application/json",
                schema: new OA\Schema(
                    type: "object",
                    required: ["label"],
                    properties: [
                        new OA\Property(
                            property: "label",
                            type: "string",
                            example: "bala"
                        )
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 204,
                description: "Race modifé avec succès",
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
                                property: "label",
                                type: "string",
                                example: "bala"
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
                description: "Race non trouvé"
            )
        ]
    )]
    public function edit(
        int $id,
        Request $request
    ): JsonResponse {
        $race = $this->repository->findOneBy(['id' => $id]);

        if ($race) {
            $race = $this->serializer
                ->deserialize(
                    $request->getContent(),
                    race::class,
                    'json',
                    [AbstractNormalizer::OBJECT_TO_POPULATE => $race]
                );

            $race->setUpdatedAt(new DateTimeImmutable());

            $this->manager->flush();

            $modify = $this->serializer
                ->serialize(
                    $race,
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
            null,
            Response::HTTP_NOT_FOUND
        );
    }


    #[Route('/{id}', name: 'delete', methods: 'DELETE')]
    #[OA\Delete(
        path: "/api/race/{id}",
        summary: "Suppression de la race",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID de la race à supprimer",
                schema: new OA\Schema(
                    type: "integer"
                )
            )
        ],
        responses: [
            new OA\Response(
                response: 204,
                description: "Race supprimer avec succès",
            ),
            new OA\Response(
                response: 404,
                description: "Race non trouvé"
            )
        ]
    )]
    public function delete(int $id): JsonResponse
    {
        $race = $this->repository->findOneBy(['id' => $id]);

        if ($race) {
            $this->manager->remove($race);
            $this->manager->flush();

            return new JsonResponse(
                ['message' => 'Race supprimé avec succès'],
                Response::HTTP_OK
            );
        }

        return new JsonResponse(
            null,
            Response::HTTP_NOT_FOUND
        );
    }
}
