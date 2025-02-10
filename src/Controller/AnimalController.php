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
    public function show(int $id): JsonResponse
    {
        $animal = $this->repository->findOneBy(['id' => $id]);

        if ($animal) {
            $responseData = $this->serializer->serialize($animal, 'json');

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
    public function edit(int $id, Request $request): JsonResponse
    {
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

            $modify = $this->serializer->serialize($animal, 'json');

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
        $queryBuilder = $this->manager->getRepository(Animal::class)->createQueryBuilder('a');

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
                    'date' => $rapportVeterinaire->getDate()->format("d-m-Y"),
                    'etat' => $rapportVeterinaire->getEtat(),
                    'nourriture proposee' => $rapportVeterinaire->getNourritureProposee(),
                    'quantite nourriture' => $rapportVeterinaire->getQuantiteNourriture(),
                    'commentaire habitat' => $rapportVeterinaire->getCommentaireHabitat(),
                    'createdAt' => $rapportVeterinaire->getCreatedAt()->format("d-m-Y"),
                    'updatedAt' =>
                    $rapportVeterinaire->getUpdatedAt() ? $rapportVeterinaire
                        ->getUpdatedAt()
                        ->format("d-m-Y")
                        : null,
                ];
            }

            return [
                'id' => $animal->getId(),
                'firstname' => $animal->getFirstname(),
                'etat' => $animal->getEtat(),
                'habitat' => $animal->getHabitat(),
                'race' => $animal->getRace(),
                'rapport veterinaires' => $rapportVeterinaireData,
                'avis' => $animal->getAvis(),
                'images' => $animal->getImages(),
                'createdAt' => $animal->getCreatedAt()->format("d-m-Y"),
            ];
        }, (array) $pagination->getItems());

        // Structure de réponse avec les informations de pagination
        $data = [
            'currentPage' => $pagination->getCurrentPageNumber(),
            'totalItems' => $pagination->getTotalItemCount(),
            'itemsPerPage' => $pagination->getItemNumberPerPage(),
            'totalPages' => ceil($pagination->getTotalItemCount() / $pagination->getItemNumberPerPage()),
            'items' => $items, // Les éléments paginés formatés
        ];

        // Retourner la réponse JSON avec les données paginées
        return new JsonResponse(
            $data,
            JsonResponse::HTTP_OK
        );
    }
}
