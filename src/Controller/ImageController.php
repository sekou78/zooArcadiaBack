<?php

namespace App\Controller;

use App\Entity\Image;
use App\Repository\ImageRepository;
use App\Service\ImageUploaderService;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{BinaryFileResponse, JsonResponse, Request, Response};
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Attributes as OA;

#[Route('api/image', name: 'app_api_image_')]
final class ImageController extends AbstractController
{
    private string $uploadDir;

    public function __construct(
        private EntityManagerInterface $manager,
        private ImageRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
        private ImageUploaderService $imageUploader,
        private KernelInterface $kernel // Injection du kernel pour obtenir le répertoire
    ) {
        // Initialisation du répertoire d'upload à partir du kernel
        $this->uploadDir = $this->kernel->getProjectDir() . '/public/uploads/images/';
    }

    // Ajouter une image
    #[Route(name: 'new', methods: ['POST'])]
    #[OA\Post(
        summary: "Ajouter une nouvelle image",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: "multipart/form-data",
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(
                            property: "image",
                            type: "string",
                            format: "binary"
                        ),
                        new OA\Property(
                            property: "habitat",
                            type: "string",
                            example: "Marais"
                        ),
                        new OA\Property(
                            property: "animal",
                            type: "string",
                            example: "Sama"
                        )
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Image ajoutée avec succès",
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
                                property: "image",
                                type: "string",
                                format: "binary"
                            ),
                            new OA\Property(
                                property: "imageData",
                                type: "string",
                                format: "byte"
                            ),
                            new OA\Property(
                                property: "habitat",
                                type: "string",
                                example: "Marais"
                            ),
                            new OA\Property(
                                property: "animal",
                                type: "string",
                                example: "Sama"
                            ),
                            new OA\Property(
                                property: "filePath",
                                type: "string",
                                format: "uri"
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
                description: "Données invalides"
            ),
            new OA\Response(
                response: 500,
                description: "Erreur interne du serveur"
            )
        ]
    )]
    public function new(Request $request): JsonResponse
    {
        // Vérifier si c'est une requête multipart (fichier normal)
        $uploadedFile = $request->files->get('image');

        // Vérifier l'extension et le type MIME
        $allowedExtensions = [
            'jpg',
            'jpeg',
            'png',
            'gif',
            'webp'
        ];
        $allowedMimeTypes = [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp'
        ];

        $fileExtension = strtolower($uploadedFile->getClientOriginalExtension());
        $mimeType = $uploadedFile->getMimeType();

        if (!in_array($fileExtension, $allowedExtensions) || !in_array($mimeType, $allowedMimeTypes)) {
            return new JsonResponse(
                ['error' => 'Invalid file type'],
                Response::HTTP_BAD_REQUEST
            );
        }

        if ($uploadedFile) {
            $fileName = uniqid() . '.' . $uploadedFile->guessExtension();

            try {
                $uploadedFile->move($this->uploadDir, $fileName);
            } catch (FileException $e) {
                return new JsonResponse(['error' => 'File upload failed'], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        } else {
            // Sinon, vérifier si c'est une requête JSON avec base64
            $data = json_decode($request->getContent(), true);

            if (!isset($data['fileName']) || !isset($data['fileData'])) {
                return new JsonResponse(['error' => 'Invalid JSON data'], Response::HTTP_BAD_REQUEST);
            }

            $fileName = uniqid() . '-' . $data['fileName'];
            $filePath = $this->uploadDir . $fileName;

            // Convertir base64 en fichier réel
            $decodedData = base64_decode($data['fileData']);
            if ($decodedData === false) {
                return new JsonResponse(['error' => 'Invalid base64 data'], Response::HTTP_BAD_REQUEST);
            }

            file_put_contents($filePath, $decodedData);
        }

        // Créer une nouvelle entité Image
        $image = new Image();
        $image->setFilePath('/uploads/images/' . $fileName);
        $image->setCreatedAt(new \DateTimeImmutable());

        $this->manager->persist($image);
        $this->manager->flush();

        return new JsonResponse(
            $this->serializer->serialize($image, 'json'),
            Response::HTTP_CREATED,
            [],
            true
        );
    }

    //Afficher une image
    #[Route('/{id}', name: 'show', methods: ['GET'])]
    #[OA\Get(
        summary: "Récupèrer une image par son ID",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(
                    type: "integer"
                )
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Image retournée avec succès",
                content: new OA\MediaType(
                    mediaType: "application/json",
                    schema: new OA\Schema(
                        type: "object",
                        properties: [
                            new OA\Property(
                                property: "image",
                                type: "string",
                                format: "binary"
                            ),
                            new OA\Property(
                                property: "imageData",
                                type: "string",
                                format: "byte"
                            ),
                            new OA\Property(
                                property: "habitat",
                                type: "string",
                                example: "Marais"
                            ),
                            new OA\Property(
                                property: "animal",
                                type: "string",
                                example: "Sama"
                            ),
                            new OA\Property(
                                property: "filePath",
                                type: "string",
                                format: "uri"
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
                description: "Image non trouvée"
            )
        ]
    )]
    public function show(int $id): BinaryFileResponse
    {
        // Récupération de l'image en base de données
        $image = $this->repository->find($id);

        // Vérification de l'existence de l'image
        if (!$image) {
            throw $this->createNotFoundException('Image not found');
        }

        // Chemin absolu du fichier sur le serveur
        $imagePath = $this->getParameter('kernel.project_dir') . '/public' . $image->getFilePath();

        // Vérification de l'existence du fichier
        if (!file_exists($imagePath)) {
            throw $this->createNotFoundException('File not found');
        }

        // Retourner directement l'image en réponse HTTP
        return new BinaryFileResponse($imagePath);
    }

    //Modifier une image
    #[Route('/{id}', name: 'edit', methods: ['POST'])]
    #[OA\Post(
        summary: "Mettre à jour une image existante",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(
                    type: "integer"
                )
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: "multipart/form-data",
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(
                            property: "image",
                            type: "string",
                            format: "binary"
                        ),
                        new OA\Property(
                            property: "habitat",
                            type: "string",
                            example: "Marais"
                        ),
                        new OA\Property(
                            property: "animal",
                            type: "string",
                            example: "Sama"
                        )
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Image mise à jour",
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
                                property: "image",
                                type: "string",
                                format: "binary"
                            ),
                            new OA\Property(
                                property: "imageData",
                                type: "string",
                                format: "byte"
                            ),
                            new OA\Property(
                                property: "habitat",
                                type: "string",
                                example: "Marais"
                            ),
                            new OA\Property(
                                property: "animal",
                                type: "string",
                                example: "Sama"
                            ),
                            new OA\Property(
                                property: "filePath",
                                type: "string",
                                format: "uri"
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
                description: "Fichier invalide"
            ),
            new OA\Response(
                response: 404,
                description: "Image non trouvée"
            )
        ]
    )]
    public function edit(int $id, Request $request): Response
    {
        // Récupération de l'image en base de données
        $image = $this->repository->find($id);

        if (!$image) {
            return new JsonResponse(
                ['error' => 'Image not found'],
                Response::HTTP_NOT_FOUND
            );
        }

        // Récupérer le fichier envoyé
        $uploadedFile = $request->files->get('image');

        if (!$uploadedFile) {
            return new JsonResponse(
                ['error' => 'No file uploaded'],
                Response::HTTP_BAD_REQUEST
            );
        }

        // Vérifier l'extension et le type MIME
        $allowedExtensions = [
            'jpg',
            'jpeg',
            'png',
            'gif',
            'webp'
        ];
        $allowedMimeTypes = [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp'
        ];

        $fileExtension = strtolower($uploadedFile->getClientOriginalExtension());
        $mimeType = $uploadedFile->getMimeType();

        if (!in_array($fileExtension, $allowedExtensions) || !in_array($mimeType, $allowedMimeTypes)) {
            return new JsonResponse(
                ['error' => 'Invalid file type'],
                Response::HTTP_BAD_REQUEST
            );
        }

        // Supprimer l'ancien fichier s'il existe
        $oldFilePath = $this->getParameter('kernel.project_dir') . '/public' . $image->getFilePath();
        if (file_exists($oldFilePath)) {
            unlink($oldFilePath);
        }

        // Générer un nouveau nom de fichier
        $fileName = uniqid() . '-' . preg_replace(
            '/[^a-zA-Z0-9\._-]/',
            '',
            $uploadedFile->getClientOriginalName()
        );

        // Déplacer le fichier vers le répertoire d'upload
        try {
            if (!is_dir($this->uploadDir) && !mkdir($this->uploadDir, 0775, true)) {
                return new JsonResponse(
                    ['error' => 'Failed to create upload directory'],
                    Response::HTTP_INTERNAL_SERVER_ERROR
                );
            }
            $uploadedFile->move($this->uploadDir, $fileName);
        } catch (FileException $e) {
            return new JsonResponse(
                ['error' => 'File upload failed'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        // Mettre à jour l'image dans la base de données
        $image->setFilePath('/uploads/images/' . $fileName);
        $image->setUpdatedAt(new DateTimeImmutable());

        $this->manager->flush();

        // Chemin absolu du fichier
        $imagePath = $this->getParameter('kernel.project_dir') . '/public' . $image->getFilePath();

        // Vérification de l'existence du fichier
        if (!file_exists($imagePath)) {
            return new JsonResponse(
                ['error' => 'File not found after upload'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        // Retourner une réponse de l'image mise à jour
        return new JsonResponse([
            'id' => $image->getId(),
            'filePath' => $image->getFilePath(), // URL relative de l'image
            'updatedAt' => $image->getUpdatedAt()->format('Y-m-d H:i:s'),
            'message' => 'Image updated successfully'
        ], Response::HTTP_OK);
    }

    //Supprimer une image
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    #[OA\Delete(
        summary: "Supprimer une image",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(
                    type: "integer"
                )
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Image supprimée"
            ),
            new OA\Response(
                response: 404,
                description: "Image non trouvée"
            )
        ]
    )]
    public function delete(int $id): JsonResponse
    {
        // Récupérer l'image depuis la base de données
        $image = $this->repository->find($id);

        if (!$image) {
            return new JsonResponse(['error' => 'Image not found'], Response::HTTP_NOT_FOUND);
        }

        // Construire le chemin absolu du fichier
        $filePath = $this->getParameter('kernel.project_dir') . '/public' . $image->getFilePath();

        // Vérifier si le fichier existe et le supprimer
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Supprimer l'image de la base de données
        $this->manager->remove($image);
        $this->manager->flush();

        return new JsonResponse(
            ['message' => 'Image deleted successfully'],
            Response::HTTP_OK
        );
    }

    // Pagination des images
    #[Route('/api/rapports', name: 'list', methods: ['GET'])]
    #[OA\Get(
        summary: "Liste paginée des images",
        parameters: [
            new OA\Parameter(
                name: "page",
                in: "query",
                schema: new OA\Schema(
                    type: "integer"
                )
            ),
            new OA\Parameter(
                name: "habitat",
                in: "query",
                schema: new OA\Schema(
                    type: "string"
                )
            ),
            new OA\Parameter(
                name: "animal",
                in: "query",
                schema: new OA\Schema(
                    type: "string"
                )
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Liste paginée des images"
            ),
            new OA\Response(
                response: 400,
                description: "Requête invalide"
            )
        ]
    )]
    public function list(
        Request $request,
        PaginatorInterface $paginator
    ): JsonResponse {
        // Récupérer les paramètres de filtre
        $habitatFilter = $request->query->get('habitat');
        $animalFilter = $request->query->get('animal');

        // Création de la requête pour récupérer tous les animaux
        $queryBuilder = $this->manager->getRepository(Image::class)->createQueryBuilder('a');

        // Appliquer le filtre sur 'habitat' si le paramètre est présent
        if ($habitatFilter) {
            $queryBuilder->andWhere('a.habitat LIKE :habitat')
                ->setParameter('habitat', '%' . $habitatFilter . '%');
        }

        // Appliquer le filtre sur 'animal' si le paramètre est présent
        if ($animalFilter) {
            $queryBuilder->andWhere('a.animal LIKE :animal')
                ->setParameter('animal', '%' . $animalFilter . '%');
        }

        // Pagination avec le paginator
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1), // Page actuelle (par défaut 1)
            5 // Nombre d'éléments par page
        );

        // Formater les résultats
        $items = array_map(
            function ($animal) {
                $formattedItem = [
                    'id' => $animal->getId(),
                    'habitat' => $animal->getHabitat(),
                    'animal' => $animal->getAnimal(),
                    'Image data' => $animal->getImageData(),
                    'file path' => $animal->getFilePath(),
                    'createdAt' => $animal->getCreatedAt()->format("d-m-Y"),
                ];

                // Ajouter updatedAt uniquement si non null
                if ($animal->getUpdatedAt() !== null) {
                    $formattedItem['updatedAt'] = $animal->getUpdatedAt()->format("d-m-Y H:i:s");
                }
                return $formattedItem;
            },
            (array) $pagination->getItems()
        );

        // Structure de réponse avec les informations de pagination
        $data = [
            'currentPage' => $pagination->getCurrentPageNumber(),
            'totalItems' => $pagination->getTotalItemCount(),
            'itemsPerPage' => $pagination->getItemNumberPerPage(),
            'totalPages' => ceil($pagination->getTotalItemCount() / $pagination->getItemNumberPerPage()),
            'items' => $items, // Les éléments paginés formatés
        ];

        // Retourner la réponse JSON avec les données paginées
        return new JsonResponse($data, JsonResponse::HTTP_OK);
    }
}
