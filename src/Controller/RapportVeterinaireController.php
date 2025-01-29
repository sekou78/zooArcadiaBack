<?php

namespace App\Controller;

use App\Entity\RapportVeterinaire;
use App\Repository\RapportVeterinaireRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
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
    public function new(Request $request): JsonResponse
    {
        $rapportVeterinaire = $this->serializer->deserialize($request->getContent(), RapportVeterinaire::class, 'json');
        $rapportVeterinaire->setCreatedAt(new DateTimeImmutable());

        $this->manager->persist($rapportVeterinaire);
        $this->manager->flush();

        $responseData = $this->serializer->serialize($rapportVeterinaire, 'json');
        $location = $this->urlGenerator->generate(
            'app_api_rapportVeterinaire_show',
            ['id' => $rapportVeterinaire->getId()],
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
    public function show(int $id, Request $request): JsonResponse
    {
        $rapportVeterinaire = $this->repository->findOneBy(['id' => $id]);

        if ($rapportVeterinaire) {
            $responseData = $this->serializer->serialize($rapportVeterinaire, 'json');

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
        $rapportVeterinaire = $this->repository->findOneBy(['id' => $id]);

        if ($rapportVeterinaire) {
            $rapportVeterinaire = $this->serializer->deserialize(
                $request->getContent(),
                rapportVeterinaire::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $rapportVeterinaire]
            );

            $rapportVeterinaire->setUpdatedAt(new DateTimeImmutable());

            $this->manager->flush();

            $modify = $this->serializer->serialize($rapportVeterinaire, 'json');

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
        $rapportVeterinaire = $this->repository->findOneBy(['id' => $id]);

        if ($rapportVeterinaire) {
            $this->manager->remove($rapportVeterinaire);
            $this->manager->flush();

            return new JsonResponse(
                ['message' => 'Rapport veterinaire deleted successfully'],
                Response::HTTP_OK
            );
        }

        return new JsonResponse(
            null,
            Response::HTTP_NOT_FOUND
        );
    }

    #[Route('/vet-reports', name: 'vet_reports', methods: 'GET')]
    #[IsGranted('ROLE_ADMIN')]
    public function getVetReports(Request $request): JsonResponse
    {
        $criteria = $request->query->all(); // Filtrer par critères (animal, date, etc.)
        $reports = $this->manager->getRepository(RapportVeterinaire::class)->findBy($criteria);

        $responseData = $this->serializer->serialize(
            $reports,
            'json'
        );

        return new JsonResponse(
            $responseData,
            Response::HTTP_OK,
            [],
            true
        );
    }

    // #[Route('/api/rapport-veterinaire', name: 'app_rapport_veterinaire', methods: 'POST')]
    // #[IsGranted('ROLE_VETERINAIRE')]
    // public function enregistrerRapportVeterinaire(
    //     Request $request,
    //     LoggerInterface $logger
    // ): JsonResponse {
    //     $data = json_decode($request->getContent(), true);

    //     $rapport = new RapportVeterinaire();
    //     $rapport->setEtatAnimal($data['etatAnimal']);
    //     $rapport->setNourriture($data['nourriture']);
    //     $rapport->setQuantite($data['quantite']);
    //     $rapport->setUser($this->getUser());

    //     $this->manager->persist($rapport);
    //     $this->manager->flush();

    //     $logger->info('Rapport vétérinaire enregistré', ['user' => $this->getUser()->getEmail()]);

    //     return new JsonResponse(['message' => 'Rapport vétérinaire enregistré'], Response::HTTP_CREATED);
    // }



    #[Route('/api/services', name: 'list', methods: ['GET'])]
    public function list(
        Request $request,
        PaginatorInterface $paginator
    ): JsonResponse {
        $queryBuilder = $this->manager->getRepository(RapportVeterinaire::class)->createQueryBuilder('s');

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );

        $data = [
            'currentPage' => $pagination->getCurrentPageNumber(),
            'totalItems' => $pagination->getTotalItemCount(),
            'itemsPerPage' => $pagination->getItemNumberPerPage(),
            'totalPages' => ceil($pagination->getTotalItemCount() / $pagination->getItemNumberPerPage()),
            'items' => $pagination->getItems(),
        ];

        return new JsonResponse($data, JsonResponse::HTTP_OK);
    }
}
