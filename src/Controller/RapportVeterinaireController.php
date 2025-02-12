<?php

namespace App\Controller;

use App\Entity\Animal;
use App\Entity\RapportVeterinaire;
use App\Repository\RapportVeterinaireRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Attributes as OA;

#[Route('api/rapportVeterinaire', name: 'app_api_rapportVeterinaire_')]
final class RapportVeterinaireController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private RapportVeterinaireRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator
    ) {}

    #[Route(name: 'new', methods: 'POST')]
    #[OA\Post(
        path: "/api/rapportVeterinaire",
        summary: "Créer un rapport vétérinaire",
        description: "Créer un rapport vétérinaire pour un animal",
        requestBody: new OA\RequestBody(
            required: true,
            description: "Données du rapport vétérinaire à créer",
            content: new OA\MediaType(
                mediaType: "application/json",
                schema: new OA\Schema(
                    type: "object",
                    required: ["animal", "etat"],
                    properties: [
                        new OA\Property(
                            property: "animal",
                            type: "integer",
                            example: 1
                        ),
                        new OA\Property(
                            property: "etat",
                            type: "string",
                            example: "Bon"
                        ),
                        new OA\Property(
                            property: "nourritureProposee",
                            type: "string",
                            example: "Croquettes"
                        ),
                        new OA\Property(
                            property: "quantiteNourriture",
                            type: "number",
                            format: "float",
                            example: 150.5
                        ),
                        new OA\Property(
                            property: "commentaireHabitat",
                            type: "string",
                            example: "L'habitat est propre."
                        ),
                        new OA\Property(
                            property: "date",
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
                response: 201,
                description: "Rapport vétérinaire créé avec succès",
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
                                property: "date",
                                type: "string",
                                format: "date-time",
                                example: "10-10-2025"
                            ),
                            new OA\Property(
                                property: "etat",
                                type: "string",
                                example: "Bon"
                            ),
                            new OA\Property(
                                property: "nourritureProposee",
                                type: "string",
                                example: "Croquettes"
                            ),
                            new OA\Property(
                                property: "quantiteNourriture",
                                type: "number",
                                format: "float",
                                example: 150.5
                            ),
                            new OA\Property(
                                property: "commentaireHabitat",
                                type: "string",
                                example: "L'habitat est propre."
                            ),
                            new OA\Property(
                                property: "animal",
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
                                        example: "Bamba"
                                    ),
                                    new OA\Property(
                                        property: "race",
                                        type: "string",
                                        example: "Lion"
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
                description: "Requête invalide"
            )
        ]
    )]
    public function new(
        Request $request,
        Security $security,
        EntityManagerInterface $manager,
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
                ['error' => "L'utilisateur n'est pas trouvé"],
                Response::HTTP_BAD_REQUEST
            );
        }

        // Désérialisation du JSON reçu dans un objet RapportVeterinaire
        $rapportVeterinaire = $this->serializer->deserialize(
            $request->getContent(),
            RapportVeterinaire::class,
            'json',
            ['groups' => ['rapportVeterinaire:write']] // Appliquer le groupe d'écriture
        );

        $data = json_decode($request->getContent(), true);

        $rapportVeterinaire->setDate(new \DateTimeImmutable($data['date'] ?? 'now'));
        $rapportVeterinaire->setEtat($data['etat'] ?? null);
        $rapportVeterinaire->setNourritureProposee($data['nourritureProposee'] ?? null);
        $rapportVeterinaire->setQuantiteNourriture($data['quantiteNourriture'] ?? null);
        $rapportVeterinaire->setCommentaireHabitat($data['commentaireHabitat'] ?? null);

        // Assigner l'animal au rapport vétérinaire
        if (isset($data['animal'])) {
            $animal = $manager->getRepository(Animal::class)->find($data['animal']);
            if ($animal) {
                $rapportVeterinaire->setAnimal($animal);
            } else {
                return new JsonResponse(
                    ['error' => 'Animal non trouvé'],
                    Response::HTTP_BAD_REQUEST
                );
            }
        }

        // Assigner l'utilisateur au rapport vétérinaire via la méthode addUser()
        $rapportVeterinaire->setVeterinaire($user);

        $rapportVeterinaire->setCreatedAt(new DateTimeImmutable());

        $this->manager->persist($rapportVeterinaire);
        $this->manager->flush();

        // Sérialiser l'objet pour renvoyer une réponse JSON
        $responseData = $this->serializer->serialize(
            $rapportVeterinaire,
            'json',
            ['groups' => ['rapport:read']] // Appliquer le groupe de lecture pour la réponse
        );

        // Générer l'URL pour accéder au rapport créé
        $location = $this->urlGenerator->generate(
            'app_api_rapportVeterinaire_show',
            ['id' => $rapportVeterinaire->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );

        // Retourner une réponse JSON
        return new JsonResponse(
            // ['message' => 'Rapport Veterinaire registered successfully']
            //Pour le test à supprimer avant production (mise en ligne)
            $responseData,
            Response::HTTP_CREATED,
            ["location" => $location],
            true
        );
    }

    #[Route('/{id}', name: 'show', methods: 'GET')]
    // #[IsGranted('ROLE_ADMIN')]
    #[OA\Get(
        path: "/api/rapportVeterinaire/{id}",
        summary: "Afficher un rapport vétérinaire",
        description: "Afficher les détails d'un rapport vétérinaire via son ID",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID du rapport vétérinaire",
                schema: new OA\Schema(
                    type: "integer"
                )
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Détails du rapport vétérinaire",
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
                                property: "date",
                                type: "string",
                                format: "date-time",
                                example: "10-10-2025"
                            ),
                            new OA\Property(
                                property: "etat",
                                type: "string",
                                example: "Bon"
                            ),
                            new OA\Property(
                                property: "nourritureProposee",
                                type: "string",
                                example: "Croquettes"
                            ),
                            new OA\Property(
                                property: "quantiteNourriture",
                                type: "number",
                                format: "float",
                                example: 150.5
                            ),
                            new OA\Property(
                                property: "commentaireHabitat",
                                type: "string",
                                example: "L'habitat est propre."
                            ),
                            new OA\Property(
                                property: "animal",
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
                                        example: "Bamba"
                                    ),
                                    new OA\Property(
                                        property: "race",
                                        type: "string",
                                        example: "Lion"
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
        // Recherche du rapport vétérinaire par ID
        $rapportVeterinaire = $this->repository->findOneBy(['id' => $id]);

        // Si le rapport existe, on le sérialise et on retourne une réponse JSON
        if ($rapportVeterinaire) {
            $responseData = $this->serializer->serialize(
                $rapportVeterinaire,
                'json',
                ['groups' => 'rapport:read']
            );
            return new JsonResponse(
                $responseData,
                Response::HTTP_OK,
                [],
                true
            );
        }

        // Si le rapport n'existe pas, on retourne une erreur 404
        return new JsonResponse(
            null,
            Response::HTTP_NOT_FOUND
        );
    }

    #[Route('/{id}', name: 'edit', methods: 'PUT')]
    #[OA\Put(
        path: "/api/rapportVeterinaire/{id}",
        summary: "Mettre à jour un rapport vétérinaire",
        description: "Mettre à jour un rapport vétérinaire",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID du rapport vétérinaire",
                schema: new OA\Schema(
                    type: "integer"
                )
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            description: "Données du rapport vétérinaire à mettre à jour",
            content: new OA\MediaType(
                mediaType: "application/json",
                schema: new OA\Schema(
                    type: "object",
                    required: ["etat", "animal"],
                    properties: [
                        new OA\Property(
                            property: "id",
                            type: "integer",
                            example: 1
                        ),
                        new OA\Property(
                            property: "date",
                            type: "string",
                            format: "date-time",
                            example: "10-10-2025"
                        ),
                        new OA\Property(
                            property: "etat",
                            type: "string",
                            example: "Bon"
                        ),
                        new OA\Property(
                            property: "nourritureProposee",
                            type: "string",
                            example: "Croquettes"
                        ),
                        new OA\Property(
                            property: "quantiteNourriture",
                            type: "number",
                            format: "float",
                            example: 150.5
                        ),
                        new OA\Property(
                            property: "commentaireHabitat",
                            type: "string",
                            example: "L'habitat est propre."
                        ),
                        new OA\Property(
                            property: "animal",
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
                                    example: "Bamba"
                                ),
                                new OA\Property(
                                    property: "race",
                                    type: "string",
                                    example: "Lion"
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
        responses: [
            new OA\Response(
                response: 200,
                description: "Rapport vétérinaire mis à jour avec succès"
            ),
            new OA\Response(
                response: 400,
                description: "Données invalides"
            ),
            new OA\Response(
                response: 404,
                description: "Rapport vétérinaire non trouvé"
            )
        ]
    )]
    public function edit(int $id, Request $request): JsonResponse
    {
        // Recherche du rapport vétérinaire existant
        $rapportVeterinaire = $this->repository->findOneBy(['id' => $id]);

        if (!$rapportVeterinaire) {
            return new JsonResponse(
                ['error' => 'Rapport vétérinaire non trouvé'],
                Response::HTTP_NOT_FOUND
            );
        }

        // Récupération et décodage des données JSON
        $data = json_decode(
            $request->getContent(),
            true
        );

        if (!$data) {
            return new JsonResponse(
                ['error' => 'Données JSON invalides'],
                Response::HTTP_BAD_REQUEST
            );
        }

        // Mise à jour des champs avec vérification
        if (isset($data['date'])) {
            $rapportVeterinaire->setDate(new \DateTimeImmutable($data['date']));
        }
        if (isset($data['etat'])) {
            $rapportVeterinaire->setEtat($data['etat']);
        }
        if (isset($data['nourritureProposee'])) {
            $rapportVeterinaire->setNourritureProposee($data['nourritureProposee']);
        }
        if (isset($data['quantiteNourriture'])) {
            $rapportVeterinaire->setQuantiteNourriture((float) $data['quantiteNourriture']);
        }
        if (isset($data['commentaireHabitat'])) {
            $rapportVeterinaire->setCommentaireHabitat($data['commentaireHabitat']);
        }

        // Mise à jour de l'animal si précisé dans la requête
        if (isset($data['animal'])) {
            $animal = $this->manager->getRepository(Animal::class)->find($data['animal']);
            if ($animal) {
                $rapportVeterinaire->setAnimal($animal);
            } else {
                return new JsonResponse(
                    ['error' => 'Animal non trouvé'],
                    Response::HTTP_BAD_REQUEST
                );
            }
        }

        // Mise à jour de la date de modification
        $rapportVeterinaire->setUpdatedAt(new DateTimeImmutable());

        // Sauvegarde des modifications
        $this->manager->flush();

        // Sérialisation avec les groupes de lecture
        $responseData = $this->serializer->serialize(
            $rapportVeterinaire,
            'json',
            ['groups' => ['rapport:read']]
        );

        return new JsonResponse(
            $responseData,
            Response::HTTP_OK,
            [],
            true
        );
    }


    #[Route('/{id}', name: 'delete', methods: 'DELETE')]
    #[OA\Delete(
        path: "/api/rapportVeterinaire/{id}",
        summary: "Supprimer un rapport vétérinaire",
        description: "Supprimer un rapport vétérinaire via son ID.",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID du rapport vétérinaire",
                schema: new OA\Schema(
                    type: "integer"
                )
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Rapport vétérinaire supprimé avec succès"
            ),
            new OA\Response(
                response: 404,
                description: "Rapport vétérinaire non trouvé"
            )
        ]
    )]
    public function delete(int $id): JsonResponse
    {
        // Recherche du rapport vétérinaire à supprimer
        $rapportVeterinaire = $this->repository->findOneBy(['id' => $id]);

        // Si le rapport existe, on le supprime
        if ($rapportVeterinaire) {
            $this->manager->remove($rapportVeterinaire);
            $this->manager->flush();

            // Retourne un message de succès
            return new JsonResponse(
                ['message' => 'Rapport vétérinaire supprimé avec succès'],
                Response::HTTP_OK
            );
        }

        // Si le rapport n'existe pas, on retourne une erreur 404
        return new JsonResponse(
            null,
            Response::HTTP_NOT_FOUND
        );
    }

    // Pagination des rapports vétérinaires
    #[Route('/api/rapports', name: 'list', methods: ['GET'])]
    #[OA\Get(
        path: "/api/rapportVeterinaire/api/rapports",
        summary: 'Liste des rapports vétérinaires avec pagination et filtrage',
        description: 'Récupérer une liste paginée des rapports vétérinaires avec des filtres'
    )]
    #[OA\Parameter(
        name: 'page',
        in: 'query',
        description: 'Numéro de la page à récupérer (par défaut 1)',
        required: false,
        schema: new OA\Schema(
            type: 'integer',
            default: 1
        )
    )]
    #[OA\Parameter(
        name: 'date',
        in: 'query',
        description: 'Filtrer par date du rapport vétérinaire',
        required: false,
        schema: new OA\Schema(
            type: 'string',
            format: 'date'
        )
    )]
    #[OA\Parameter(
        name: 'etat',
        in: 'query',
        description: 'Filtrer par état du rapport vétérinaire',
        required: false,
        schema: new OA\Schema(
            type: 'string'
        )
    )]
    #[OA\Parameter(
        name: 'animal',
        in: 'query',
        description: "Filtrer par ID de l'animal lié au rapport vétérinaire",
        required: false,
        schema: new OA\Schema(
            type: 'integer'
        )
    )]
    #[OA\Parameter(
        name: 'nourriture_proposee',
        in: 'query',
        description: 'Filtrer par type de nourriture proposée',
        required: false,
        schema: new OA\Schema(
            type: 'string'
        )
    )]
    #[OA\Parameter(
        name: 'quantiteNourriture',
        in: 'query',
        description: 'Filtrer par quantité de nourriture proposée',
        required: false,
        schema: new OA\Schema(
            type: 'string'
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Réponse avec la liste paginée des rapports vétérinaires',
        content: new OA\JsonContent(
            type: 'object',
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
                    property: 'items',
                    type: 'array',
                    items: new OA\Items(
                        type: 'object',
                        properties: [
                            new OA\Property(
                                property: 'id',
                                type: 'integer',
                                example: 1
                            ),
                            new OA\Property(
                                property: 'date',
                                type: 'string',
                                format: 'date',
                                example: "10-10-2025"
                            ),
                            new OA\Property(
                                property: "etat",
                                type: "string",
                                example: "Bon"
                            ),
                            new OA\Property(
                                property: "nourritureProposee",
                                type: "string",
                                example: "Croquettes"
                            ),
                            new OA\Property(
                                property: "quantiteNourriture",
                                type: "number",
                                format: "float",
                                example: 150.5
                            ),
                            new OA\Property(
                                property: "animal",
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
                                        example: "Bamba"
                                    ),
                                    new OA\Property(
                                        property: "race",
                                        type: "string",
                                        example: "Lion"
                                    )
                                ],
                            ),
                            new OA\Property(
                                property: "commentaireHabitat",
                                type: "string",
                                example: "L'habitat est propre."
                            ),
                            new OA\Property(
                                property: "createdAt",
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
            ]
        )
    )]
    #[OA\Response(
        response: 400,
        description: 'Requête invalide'
    )]
    #[OA\Response(
        response: 404,
        description: 'Ressource non trouvée'
    )]
    public function list(
        Request $request,
        PaginatorInterface $paginator
    ): JsonResponse {
        // Récupérer les paramètres de filtre
        $dateFilter = $request->query->get('date');
        $etatFilter = $request->query->get('etat');
        $animalFilter = $request->query->get('animal');
        $nourritureProposeeFilter = $request->query->get('nourriture proposee');
        $quantiteNourritureFilter = $request->query->get('quantiteNourriture');

        // Création de la requête avec jointure sur Animal
        $queryBuilder = $this->manager->getRepository(RapportVeterinaire::class)
            ->createQueryBuilder('a')
            ->leftJoin('a.animal', 'animal') // Jointure avec l'entité Animal
            ->addSelect('animal'); // Sélectionner aussi les données de l'animal

        // Appliquer le filtre sur 'date' si le paramètre est présent
        if ($dateFilter) {
            $queryBuilder->andWhere('a.date LIKE :date')
                ->setParameter('date', '%' . $dateFilter . '%');
        }

        // Appliquer le filtre sur 'etat' si le paramètre est présent
        if ($etatFilter) {
            $queryBuilder->andWhere('a.etat LIKE :etat')
                ->setParameter('etat', '%' . $etatFilter . '%');
        }

        // Appliquer le filtre sur 'animal' si le paramètre est présent
        if ($animalFilter) {
            $queryBuilder->andWhere('a.animal LIKE :animal')
                ->setParameter('animal', '%' . $animalFilter . '%');
        }

        // Appliquer le filtre sur 'nourriture proposee' si le paramètre est présent
        if ($nourritureProposeeFilter) {
            $queryBuilder->andWhere('a.nourritureProposee LIKE :nourritureProposee')
                ->setParameter('nourritureProposee', '%' . $nourritureProposeeFilter . '%');
        }

        // Appliquer le filtre sur 'quantiteNourriture' si le paramètre est présent
        if ($quantiteNourritureFilter) {
            $queryBuilder->andWhere('a.quantiteNourriture LIKE :quantiteNourriture')
                ->setParameter('quantiteNourriture', '%' . $quantiteNourritureFilter . '%');
        }

        // Pagination avec le paginator
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1), // Page actuelle (par défaut 1)
            10 // Nombre d'éléments par page
        );

        // Formater les résultats
        $items = array_map(function ($rapportVeterinaire) {
            $animal = $rapportVeterinaire->getAnimal(); // Récupération de l'animal
            $updatedAt = $rapportVeterinaire->getUpdatedAt(); // Récupération de updatedAt
            return [
                'id' => $rapportVeterinaire->getId(),
                'date' => $rapportVeterinaire->getDate()->format("d-m-Y"),
                'etat' => $rapportVeterinaire->getEtat(),
                'nourriture proposee' => $rapportVeterinaire->getNourritureProposee(),
                'quantite nourriture' => $rapportVeterinaire->getquantiteNourriture(),
                'animal' => $animal ? [ // Vérification avant d'accéder aux données de l'animal
                    'id' => $animal->getId(),
                    'nom' => $animal->getFirstname(),
                    'race' => $animal->getRace(),
                ] : null, // Si aucun animal n'est associé, retourne `null`
                'commentaire habitat' => $rapportVeterinaire->getCommentaireHabitat(),
                'createdAt' => $rapportVeterinaire->getCreatedAt()->format("d-m-Y"),
                'updatedAt' => $updatedAt ? $updatedAt->format("d-m-Y") : null, // Vérification avant d'ajouter
            ];
        }, (array) $pagination->getItems());

        // Structure de réponse avec les informations de pagination
        $data = [
            'currentPage' => $pagination->getCurrentPageNumber(),
            'totalItems' => $pagination->getTotalItemCount(),
            'itemsPerPage' => $pagination->getItemNumberPerPage(),
            'totalPages' => ceil(
                $pagination->getTotalItemCount() / $pagination->getItemNumberPerPage()
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
