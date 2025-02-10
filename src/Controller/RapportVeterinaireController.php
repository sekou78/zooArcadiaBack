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
    public function list(
        Request $request,
        PaginatorInterface $paginator
    ): JsonResponse {
        // Création de la requête pour récupérer tous les rapports vétérinaires
        $queryBuilder = $this->manager->getRepository(
            RapportVeterinaire::class
        )->createQueryBuilder('s');

        // Pagination avec le paginator
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1), // Page actuelle (par défaut 1)
            10 // Nombre d'éléments par page
        );

        // Structure de réponse avec les informations de pagination
        $data = [
            'currentPage' => $pagination->getCurrentPageNumber(),
            'totalItems' => $pagination->getTotalItemCount(),
            'itemsPerPage' => $pagination->getItemNumberPerPage(),
            'totalPages' => ceil(
                $pagination->getTotalItemCount() / $pagination->getItemNumberPerPage()
            ),
            'items' => $pagination->getItems(), // Les éléments paginés
        ];

        // Retourner la réponse JSON avec les données paginées
        return new JsonResponse(
            $data,
            JsonResponse::HTTP_OK
        );
    }
}
