<?php

namespace App\Controller;

use App\Entity\Animal;
use App\Entity\Habitat;
use App\Repository\HabitatRepository;
use App\Service\ConsultationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

#[Route('/pb/espacePublic', name: 'app_pb_espacePublic_')]
final class EspacePublicController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private HabitatRepository $habitatRepository
    ) {}

    #[Route('/animaux/{nom}', name: 'animauxDetail', methods: 'GET')]
    #[OA\Get(
        path: "/espacePublic/animaux/{nom}",
        summary: "Récupérer les détails d'un animal",
        description: "Retourne les informations d'un animal en fonction de son nom",
        parameters: [
            new OA\Parameter(
                name: "nom",
                description: "Nom de l'animal",
                in: "path",
                required: true,
                schema: new OA\Schema(
                    type: "string",
                    example: "Bamba"
                )
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Détails de l'animal récupérés avec succès",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(
                            property: "nom",
                            type: "string",
                            example: "Bamba"
                        ),
                        new OA\Property(
                            property: "race",
                            type: "string",
                            example: "Labrador"
                        ),
                        new OA\Property(
                            property: "etat",
                            type: "string",
                            example: "Bonne santé"
                        ),
                        new OA\Property(
                            property: "habitat",
                            type: "string",
                            example: "Marais"
                        ),
                        new OA\Property(
                            property: "rapport_veterinaire",
                            type: "object",
                            nullable: true,
                            properties: [
                                new OA\Property(
                                    property: "date",
                                    type: "string",
                                    example: "12-02-2024"
                                ),
                                new OA\Property(
                                    property: "etat",
                                    type: "string",
                                    example: "Fatigué"
                                ),
                                new OA\Property(
                                    property: "commentaire",
                                    type: "string",
                                    example: "Doit se reposer"
                                )
                            ]
                        ),
                        new OA\Property(
                            property: "image",
                            type: "array",
                            items: new OA\Items(
                                type: "string",
                                example: "https://example.com/image.jpg"
                            )
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Animal n'existe pas'",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(
                            property: "error",
                            type: "string",
                            example: "Animal non trouvé"
                        )
                    ]
                )
            )
        ]
    )]
    public function AnimauxDetail(
        string $nom,
        ConsultationService $consultationService
    ): JsonResponse {
        // Sécurisation de l'entrée utilisateur (évite les injections)
        $nom = htmlspecialchars($nom, ENT_QUOTES, 'UTF-8');

        // Récupérer l'animal depuis la base de données relationnelle (MySQL/PostgreSQL)
        $animal = $this->manager
            ->getRepository(Animal::class)
            ->findOneBy(
                ['firstname' => $nom]
            );

        if (!$animal) {
            return new JsonResponse(
                ['error' => 'Animal non trouvé'],
                JsonResponse::HTTP_NOT_FOUND
            );
        }

        // Incrémenter le compteur de consultations dans MongoDB
        $consultationService->incrementConsultation(
            $animal->getFirstname()
        );

        // Récupérer le dernier rapport vétérinaire
        $rapport = $animal
            ->getRapportVeterinaires()
            ->last();

        // Récupérer les images en filtrant les URLs invalides
        $images = [];
        if (is_iterable($animal->getImages())) {
            foreach ($animal->getImages() as $image) {
                $url = $image->getFilePath();
                if (filter_var($url, FILTER_VALIDATE_URL)) {
                    $images[] = $url;
                }
            }
        }

        // Structurer les données à retourner
        $data = [
            'nom' => $animal->getFirstname(),
            'race' => $animal->getRace(),
            'etat' => $animal->getEtat(),
            'habitat' => $animal->getHabitat(),
            'rapport_veterinaire' => $rapport ? [
                'date' => $rapport->getDate()->format("d-m-Y"),
                'etat' => $rapport->getEtat(),
                'commentaire' => $rapport->getCommentaireHabitat()
            ] : null
        ];

        // Ajouter l'image seulement si elle n'est pas vide
        if (!empty($images)) {
            $data['image'] = $images;
        }

        // Supprimer les clés contenant des valeurs null
        foreach ($data as $key => $value) {
            if ($value === null) {
                unset($data[$key]);
            }
        }

        // Supprimer les valeurs null du sous-tableau rapport_veterinaire
        if (isset($data['rapport_veterinaire'])) {
            foreach ($data['rapport_veterinaire'] as $key => $value) {
                if ($value === null) {
                    unset($data['rapport_veterinaire'][$key]);
                }
            }

            // Si après nettoyage, le tableau est vide, on le supprime aussi
            if (empty($data['rapport_veterinaire'])) {
                unset($data['rapport_veterinaire']);
            }
        }

        return new JsonResponse(
            $data,
            JsonResponse::HTTP_OK
        );
    }

    #[Route('/habitats', name: 'habitats', methods: 'GET')]
    #[OA\Get(
        path: "/habitats",
        summary: "Récupérer la liste des habitats visibles",
        description: "Cette route retourne tous les habitats"
    )]
    #[OA\Response(
        response: 200,
        description: "Liste des habitats visibles",
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
        // Récupérer tous les habitats avec leurs images
        $habitats = $this->habitatRepository->findAllWithImages();

        // Transformer les données pour éviter d’exposer trop d’informations
        $data = array_map(
            function ($habitat) {
                // Filtrer les images pour éviter les URLs invalides
                $images = [];
                if (is_iterable($habitat->getImages())) {
                    foreach ($habitat->getImages() as $image) {
                        $url = $image->getFilePath();  // Récupère le chemin de l'image
                        if (filter_var($url, FILTER_VALIDATE_URL)) {
                            $images[] = $url;  // Ajoute l'image si l'URL est valide
                        }
                    }
                }

                // Filtrer les animaux
                $animaux = [];
                if (is_iterable($habitat->getAnimals())) {
                    foreach ($habitat->getAnimals() as $animal) {
                        $animaux[] = [
                            "nom" => $animal->getFirstname(),
                            "race" => $animal->getRace(),
                            "etat" => $animal->getEtat(),
                        ];
                    }
                }

                return [
                    "nom" => $habitat->getName(),
                    "images" => !empty($images) ? $images : null,  // Affiche les images si elles existent
                    "description" => $habitat->getDescription(),
                    "animaux" => $animaux,
                ];
            },
            $habitats
        );

        return new JsonResponse(
            $data,
            JsonResponse::HTTP_OK
        );
    }
}
