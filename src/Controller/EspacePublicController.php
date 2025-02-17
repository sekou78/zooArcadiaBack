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

#[Route('api/espacePublic', name: 'app_api_espacePublic_')]
final class EspacePublicController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private HabitatRepository $habitatRepository
    ) {}

    #[Route('/animaux/{nom}', name: 'animauxDetail', methods: 'GET')]
    public function AnimauxDetail(
        string $nom,
        ConsultationService $consultationService
    ): JsonResponse {
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
        $consultationService->incrementConsultation($animal->getFirstname());

        // Récupérer le dernier rapport vétérinaire
        $rapport = $animal->getRapportVeterinaires()->last();

        // Structurer les données à retourner
        $data = [
            'nom' => $animal->getFirstname(),
            'race' => $animal->getRace(),
            'image' => $animal->getImages(),
            'habitat' => $animal->getHabitat(),
            'rapport_veterinaire' => $rapport ? [
                'date' => $rapport->getDate()->format("d-m-Y"),
                'etat' => $rapport->getEtat(),
                'commentaire' => $rapport->getCommentaireHabitat()
            ] : null
        ];

        return new JsonResponse($data, JsonResponse::HTTP_OK);
    }

    #[Route('/habitats', name: 'habitats', methods: 'GET')]
    #[OA\Get(
        path: "/api/habitat/",
        summary: "Récupérer la liste des habitat visibles",
        description: "Cette route retourne tous les habitats"
    )]
    #[OA\Response(
        response: 200,
        description: "Liste des habitat visibles",
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
        $habitats = $this->habitatRepository->findAll();

        $data = array_map(
            function (Habitat $habitat) {
                return [
                    "nom" => $habitat->getName(),
                    "images" => $habitat->getImages(),
                    "description" => $habitat->getDescription(),
                    "liste d'animaux" => $habitat->getAnimals(),
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
