<?php

namespace App\Controller;

use App\Entity\Animal;
use App\Repository\AnimalRepository;
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
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('api/animal', name: 'app_api_animal_')]
final class AnimalController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private AnimalRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator
    ) {}

    #[Route(name: 'new', methods: 'POST')]
    #[IsGranted('ROLE_ADMIN')]
    #[OA\Post(
        path: "/api/animal",
        summary: "Créer un animal",
        requestBody: new OA\RequestBody(
            required: true,
            description: "Données de l'animal à créer",
            content: new OA\MediaType(
                mediaType: "application/json",
                schema: new OA\Schema(
                    type: "object",
                    required: ["firstname", "etat"],
                    properties: [
                        new OA\Property(
                            property: "firstname",
                            type: "string",
                            example: "Bamba"
                        ),
                        new OA\Property(
                            property: "etat",
                            type: "string",
                            example: "Sain"
                        )
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Animal créer avec succès",
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
                                property: "firtname",
                                type: "string",
                                example: "Bamba"
                            ),
                            new OA\Property(
                                property: "etat",
                                type: "string",
                                example: "Sain"
                            ),
                            new OA\Property(
                                property: "habitat",
                                type: "string",
                                example: "marais"
                            ),
                            new OA\Property(
                                property: "race",
                                type: "string",
                                example: "A corne"
                            ),
                            new OA\Property(
                                property: "Rapport Veterinaire",
                                type: "string",
                                example: "En bonne santé"
                            ),
                            new OA\Property(
                                property: "avis",
                                type: "string",
                                example: "Joyeux"
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
        $animal = $this->serializer->deserialize(
            $request->getContent(),
            Animal::class,
            'json'
        );

        $animal->setCreatedAt(new DateTimeImmutable());

        $this->manager->persist($animal);
        $this->manager->flush();

        $responseData = $this->serializer->serialize($animal, 'json');
        $location = $this->urlGenerator->generate(
            'app_api_animal_show',
            ['id' => $animal->getId()],
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
        path: "/api/animal/{id}",
        summary: "Afficher un animal par son ID",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID de l'animal à afficher",
                schema: new OA\Schema(
                    type: "integer"
                )
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Animal trouvé avec succès",
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
                                property: "firtname",
                                type: "string",
                                example: "Bamba"
                            ),
                            new OA\Property(
                                property: "etat",
                                type: "string",
                                example: "Sain"
                            ),
                            new OA\Property(
                                property: "habitat",
                                type: "string",
                                example: "marais"
                            ),
                            new OA\Property(
                                property: "race",
                                type: "string",
                                example: "A corne"
                            ),
                            new OA\Property(
                                property: "Rapport Veterinaire",
                                type: "string",
                                example: "En bonne santé"
                            ),
                            new OA\Property(
                                property: "avis",
                                type: "string",
                                example: "Joyeux"
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
                description: "Animal non trouvé"
            )
        ]
    )]
    public function show(int $id): JsonResponse
    {
        $animal = $this->repository->findOneBy(['id' => $id]);

        if ($animal) {
            $responseData = $this->serializer
                ->serialize(
                    $animal,
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
    #[IsGranted('ROLE_ADMIN')]
    #[OA\Put(
        path: "/api/animal/{id}",
        summary: "Mise à jour du animal",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID du animal à modifier",
                schema: new OA\Schema(
                    type: "integer"
                )
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            description: "Données du animal à modifier",
            content: new OA\MediaType(
                mediaType: "application/json",
                schema: new OA\Schema(
                    type: "object",
                    required: ["firstname", "etat"],
                    properties: [
                        new OA\Property(
                            property: "firstname",
                            type: "string",
                            example: "Sama"
                        ),
                        new OA\Property(
                            property: "etat",
                            type: "string",
                            example: "Bléssé"
                        )
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 204,
                description: "Animal modifé avec succès",
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
                                property: "firtname",
                                type: "string",
                                example: "Sama"
                            ),
                            new OA\Property(
                                property: "etat",
                                type: "string",
                                example: "Bléssé"
                            ),
                            new OA\Property(
                                property: "habitat",
                                type: "string",
                                example: "marais"
                            ),
                            new OA\Property(
                                property: "race",
                                type: "string",
                                example: "A corne"
                            ),
                            new OA\Property(
                                property: "Rapport Veterinaire",
                                type: "string",
                                example: "En Soin"
                            ),
                            new OA\Property(
                                property: "avis",
                                type: "string",
                                example: "Joyeux"
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
                description: "Animal non trouvé"
            )
        ]
    )]
    public function edit(
        int $id,
        Request $request
    ): JsonResponse {
        $animal = $this->repository->findOneBy(['id' => $id]);

        if ($animal) {
            $animal = $this->serializer->deserialize(
                $request->getContent(),
                Animal::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $animal]
            );

            $animal->setUpdatedAt(new DateTimeImmutable());

            $this->manager->flush();

            $modify = $this->serializer
                ->serialize(
                    $animal,
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
    #[IsGranted('ROLE_ADMIN')]
    #[OA\Delete(
        path: "/api/animal/{id}",
        summary: "Suppression de l'animal",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID du animal à supprimer",
                schema: new OA\Schema(
                    type: "integer"
                )
            )
        ],
        responses: [
            new OA\Response(
                response: 204,
                description: "Animal supprimer avec succès",
            ),
            new OA\Response(
                response: 404,
                description: "Animal non trouvé"
            )
        ]
    )]
    public function delete(int $id): JsonResponse
    {
        $animal = $this->repository->findOneBy(['id' => $id]);

        if ($animal) {
            $this->manager->remove($animal);
            $this->manager->flush();

            return new JsonResponse(
                ['message' => 'Animal deleted successfully'],
                Response::HTTP_OK
            );
        }

        return new JsonResponse(
            null,
            Response::HTTP_NOT_FOUND
        );
    }

    // Pagination des animaux
    #[Route('/api/rapports', name: 'list', methods: ['GET'])]
    #[OA\Get(
        path: '/api/animal/api/rapports',
        summary: "Liste paginée des animaux avec filtres",
        description: "Récupère une liste paginée d'animaux avec la possibilité d'ajouter des filtres"
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
        name: 'firstname',
        in: 'query',
        description: "Filtrer par prénom de l'animal",
        schema: new OA\Schema(
            type: 'string'
        )
    )]
    #[OA\Parameter(
        name: 'etat',
        in: 'query',
        description: "Filtrer par état de l\'animal",
        schema: new OA\Schema(
            type: 'string'
        )
    )]
    #[OA\Parameter(
        name: 'habitat',
        in: 'query',
        description: "Filtrer par habitat",
        schema: new OA\Schema(
            type: 'string'
        )
    )]
    #[OA\Parameter(
        name: 'race',
        in: 'query',
        description: "Filtrer par race",
        schema: new OA\Schema(
            type: 'string'
        )
    )]
    #[OA\Parameter(
        name: 'rapportVeterinaire',
        in: 'query',
        description: "Filtrer par rapport vétérinaire",
        schema: new OA\Schema(
            type: 'string'
        )
    )]
    #[OA\Parameter(
        name: 'avis',
        in: 'query',
        description: "Filtrer par avis",
        schema: new OA\Schema(
            type: 'string'
        )
    )]
    #[OA\Response(
        response: 200,
        description: "Retourne une liste paginée d'animaux",
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
                                property: 'firstname',
                                type: 'string',
                                example: 'Milo'
                            ),
                            new OA\Property(
                                property: 'etat',
                                type: 'string',
                                example: 'Bonne santé'
                            ),
                            new OA\Property(
                                property: 'habitat',
                                type: 'string',
                                example: 'Savane'
                            ),
                            new OA\Property(
                                property: 'race',
                                type: 'string',
                                example: 'Lion'
                            ),
                            new OA\Property(
                                property: 'avis',
                                type: 'string',
                                example: 'Très bon état'
                            ),
                            new OA\Property(
                                property: 'rapportVeterinaires',
                                type: 'array',
                                items: new OA\Items(
                                    properties: [
                                        new OA\Property(
                                            property: 'id',
                                            type: 'integer',
                                            example: 5
                                        ),
                                        new OA\Property(
                                            property: 'veterinaire',
                                            type: 'object',
                                            properties: [
                                                new OA\Property(
                                                    property: 'id',
                                                    type: 'integer',
                                                    example: 2
                                                ),
                                                new OA\Property(
                                                    property: 'nom',
                                                    type: 'string',
                                                    example: 'Dr. Dupont'
                                                )
                                            ]
                                        ),
                                        new OA\Property(
                                            property: 'date',
                                            type: 'string',
                                            format: 'date',
                                            example: '01-02-2024'
                                        ),
                                        new OA\Property(
                                            property: 'etat',
                                            type: 'string',
                                            example: 'Bon'
                                        ),
                                        new OA\Property(
                                            property: 'nourriture proposee',
                                            type: 'string',
                                            example: 'Viande'
                                        ),
                                        new OA\Property(
                                            property: 'quantite nourriture',
                                            type: 'string',
                                            example: '500g'
                                        ),
                                        new OA\Property(
                                            property: 'commentaire habitat',
                                            type: 'string',
                                            example: 'Habitat propre'
                                        ),
                                        new OA\Property(
                                            property: 'createdAt',
                                            type: 'string',
                                            format: 'date',
                                            example: '01-02-2024'
                                        ),
                                        new OA\Property(
                                            property: 'updatedAt',
                                            type: 'string',
                                            format: 'date',
                                            example: '05-02-2024'
                                        )
                                    ]
                                )
                            ),
                            new OA\Property(
                                property: 'images',
                                type: 'array',
                                items: new OA\Items(
                                    type: 'string'
                                )
                            ),
                            new OA\Property(
                                property: 'createdAt',
                                type: 'string',
                                format: 'date',
                                example: '01-02-2024'
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
        $firstnameFilter = $request->query->get('firstname');
        $etatFilter = $request->query->get('etat');
        $habitatFilter = $request->query->get('habitat');
        $raceFilter = $request->query->get('race');
        $rapportVeterinaireFilter = $request->query->get('rapportVeterinaire');
        $avisFilter = $request->query->get('avis');

        // Création de la requête pour récupérer tous les animaux
        $queryBuilder = $this->manager
            ->getRepository(Animal::class)
            ->createQueryBuilder('a');

        // Appliquer le filtre sur 'firstname' si le paramètre est présent
        if ($firstnameFilter) {
            $queryBuilder->andWhere('a.firstname LIKE :firstname')
                ->setParameter('firstname', '%' . $firstnameFilter . '%');
        }

        // Appliquer le filtre sur 'etat' si le paramètre est présent
        if ($etatFilter) {
            $queryBuilder->andWhere('a.etat LIKE :etat')
                ->setParameter('etat', '%' . $etatFilter . '%');
        }

        // Appliquer le filtre sur 'habitat' si le paramètre est présent
        if ($habitatFilter) {
            $queryBuilder->andWhere('a.habitat LIKE :habitat')
                ->setParameter('habitat', '%' . $habitatFilter . '%');
        }

        // Appliquer le filtre sur 'race' si le paramètre est présent
        if ($raceFilter) {
            $queryBuilder->andWhere('a.race LIKE :race')
                ->setParameter('race', '%' . $raceFilter . '%');
        }

        // Appliquer le filtre sur 'rapportVeterinaire' si le paramètre est présent
        if ($rapportVeterinaireFilter) {
            $queryBuilder->andWhere('a.rapportVeterinaire LIKE :rapportVeterinaire')
                ->setParameter('rapportVeterinaire', '%' . $rapportVeterinaireFilter . '%');
        }

        // Appliquer le filtre sur 'avis' si le paramètre est présent
        if ($avisFilter) {
            $queryBuilder->andWhere('a.avis LIKE :avis')
                ->setParameter('avis', '%' . $avisFilter . '%');
        }

        // Pagination avec le paginator
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1), // Page actuelle (par défaut 1)
            5 // Nombre d'éléments par page
        );

        // Formater les résultats
        $items = array_map(function ($animal) {
            // Utilise la méthode getRapportVeterinaires()
            $rapportsVeterinaires = $animal->getRapportVeterinaires();
            $rapportVeterinaireData = [];

            // Si l'animal a des rapports vétérinaires associés, formate-les
            foreach ($rapportsVeterinaires as $rapportVeterinaire) {
                $rapportVeterinaireData[] = [
                    'id' => $rapportVeterinaire->getId(),
                    'veterinaire' => $rapportVeterinaire->getVeterinaire() ? [
                        'id' => $rapportVeterinaire->getVeterinaire()->getId(),
                        'nom' => $rapportVeterinaire->getVeterinaire()->getNom(),
                    ] : null,
                    'date' => $rapportVeterinaire
                        ->getDate()
                        ->format("d-m-Y"),
                    'etat' => $rapportVeterinaire
                        ->getEtat(),
                    'nourriture proposee' => $rapportVeterinaire
                        ->getNourritureProposee(),
                    'quantite nourriture' => $rapportVeterinaire
                        ->getQuantiteNourriture(),
                    'commentaire habitat' => $rapportVeterinaire
                        ->getCommentaireHabitat(),
                    'createdAt' => $rapportVeterinaire
                        ->getCreatedAt()
                        ->format("d-m-Y"),
                    'updatedAt' =>
                    $rapportVeterinaire->getUpdatedAt() ? $rapportVeterinaire
                        ->getUpdatedAt()
                        ->format("d-m-Y")
                        : null,
                ];
            }

            return [
                'id' => $animal->getId(),
                'firstname' => $animal
                    ->getFirstname(),
                'etat' => $animal
                    ->getEtat(),
                'habitat' => $animal
                    ->getHabitat(),
                'race' => $animal
                    ->getRace(),
                'rapport veterinaires' => $rapportVeterinaireData,
                'avis' => $animal
                    ->getAvis(),
                'images' => $animal
                    ->getImages(),
                'createdAt' => $animal
                    ->getCreatedAt()
                    ->format("d-m-Y"),
            ];
        }, (array) $pagination->getItems());

        // Structure de réponse avec les informations de pagination
        $data = [
            'currentPage' => $pagination
                ->getCurrentPageNumber(),
            'totalItems' => $pagination
                ->getTotalItemCount(),
            'itemsPerPage' => $pagination
                ->getItemNumberPerPage(),
            'totalPages' => ceil(
                $pagination
                    ->getTotalItemCount() / $pagination
                    ->getItemNumberPerPage()
            ),
            'items' => $items, // Les éléments paginés formatés
        ];

        // Retourner la réponse JSON avec les données paginées
        return new JsonResponse(
            $data,
            JsonResponse::HTTP_OK
        );
    }
}
