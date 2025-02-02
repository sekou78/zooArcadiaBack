<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Repository\AvisRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('api/avis', name: 'app_api_avis_')]
final class AvisController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private AvisRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator
    ) {}

    #[Route(name: 'new', methods: 'POST')]
    public function new(Request $request): JsonResponse
    {
        $avis = $this->serializer->deserialize(
            $request->getContent(),
            Avis::class,
            'json'
        );

        $avis->setCreatedAt(new DateTimeImmutable());

        $avis->setVisible(false); // Définit une valeur par défaut pour `isVisible`

        $this->manager->persist($avis);
        $this->manager->flush();

        $responseData = $this->serializer->serialize($avis, 'json');
        $location = $this->urlGenerator->generate(
            'app_api_avis_show',
            ['id' => $avis->getId()],
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
        $avis = $this->repository->findOneBy(['id' => $id]);

        if ($avis) {
            $responseData = $this->serializer->serialize($avis, 'json');

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

    #[Route('/{id}', name: 'delete', methods: 'DELETE')]
    public function delete(int $id): JsonResponse
    {
        $avis = $this->repository->findOneBy(['id' => $id]);

        // Vérification si l'utilisateur a l'un des rôles requis
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_EMPLOYE')) {
            return new JsonResponse(
                ['message' => 'Access denied'],
                Response::HTTP_FORBIDDEN
            );
        }

        if ($avis) {
            $this->manager->remove($avis);
            $this->manager->flush();

            return new JsonResponse(
                ['message' => 'Avis deleted successfully'],
                Response::HTTP_OK
            );
        }

        return new JsonResponse(
            null,
            Response::HTTP_NOT_FOUND
        );
    }
}
