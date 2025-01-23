<?php

namespace App\Controller;

use App\Entity\RapportVeterinaire;
use App\Repository\RapportVeterinaireRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
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
    public function show(int $id): JsonResponse
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
}
