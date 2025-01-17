<?php

namespace App\Controller;

use App\Entity\Animal;
use App\Repository\AnimalRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
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
        )
    {
        
    }

    #[Route(name: 'new', methods: 'POST')]
    public function new(Request $request): JsonResponse
    {
        $animal = $this->serializer->deserialize($request->getContent(), Animal::class, 'json');
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
            $responseData = $this->serializer->serialize($animal,'json');

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
                animal::class,
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
                null,
                Response::HTTP_NO_CONTENT
            );
        }

        return new JsonResponse(
            null,
            Response::HTTP_NOT_FOUND
        );
    }
}
