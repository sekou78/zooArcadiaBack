<?php

namespace App\Controller;

use App\Entity\Service;
use App\Repository\ServiceRepository;
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
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('api/service', name: 'app_api_service_')]
final class ServiceController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private ServiceRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator
    ) {}

    #[Route(name: 'new', methods: 'POST')]
    #[IsGranted('ROLE_ADMIN')]
    public function new(
        Request $request,
        ValidatorInterface $validator,
    ): JsonResponse {
        $service = $this->serializer
            ->deserialize(
                $request->getContent(),
                Service::class,
                'json'
            );

        // Validation
        $errors = $validator->validate($service);
        if (count($errors) > 0) {
            return new JsonResponse(
                (string) $errors,
                Response::HTTP_BAD_REQUEST
            );
        }


        // Ajouter l'utilisateur connecté
        $service->addUtilisateur($this->getUser());

        $service->setCreatedAt(new DateTimeImmutable());

        $this->manager->persist($service);
        $this->manager->flush();

        $responseData = $this->serializer
            ->serialize(
                $service,
                'json',
                ['groups' => 'service_user_read']
            );

        $location = $this->urlGenerator->generate(
            'app_api_service_show',
            ['id' => $service->getId()],
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
        $service = $this->repository->findOneBy(['id' => $id]);

        if ($service) {
            $responseData = $this->serializer
                ->serialize(
                    $service,
                    'json'
                );

            return new JsonResponse(
                $responseData,
                Response::HTTP_OK,
                [],
                true
            );
        }

        return new JsonResponse(
            ['error' => 'Service non trouvé'],
            Response::HTTP_NOT_FOUND
        );
    }

    #[Route('/{id}', name: 'edit', methods: 'PUT')]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(
        int $id,
        Request $request
    ): JsonResponse {
        $service = $this->repository->findOneBy(['id' => $id]);

        if ($service) {
            $service = $this->serializer
                ->deserialize(
                    $request->getContent(),
                    Service::class,
                    'json',
                    [AbstractNormalizer::OBJECT_TO_POPULATE => $service]
                );

            $service->setUpdatedAt(new DateTimeImmutable());

            $this->manager->flush();

            $modify = $this->serializer
                ->serialize(
                    $service,
                    'json'
                );

            return new JsonResponse(
                $modify,
                Response::HTTP_OK,
                [],
                true
            );
        }

        return new JsonResponse(
            ['error' => 'Service non trouvé'],
            Response::HTTP_NOT_FOUND
        );
    }


    #[Route('/{id}', name: 'delete', methods: 'DELETE')]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(int $id): JsonResponse
    {
        $service = $this->repository->findOneBy(['id' => $id]);

        if ($service) {
            $this->manager->remove($service);
            $this->manager->flush();

            return new JsonResponse(
                ['message' => 'Service supprimer avec succès'],
                Response::HTTP_OK
            );
        }

        return new JsonResponse(
            ['error' => 'Service non trouvé'],
            Response::HTTP_NOT_FOUND
        );
    }

    #[Route('/api/services', name: 'list', methods: ['GET'])]
    public function list(
        Request $request,
        PaginatorInterface $paginator
    ): JsonResponse {
        $queryBuilder = $this->manager->getRepository(
            Service::class
        )->createQueryBuilder('s');

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );

        $data = [
            'currentPage' => $pagination->getCurrentPageNumber(),
            'totalItems' => $pagination->getTotalItemCount(),
            'itemsPerPage' => $pagination->getItemNumberPerPage(),
            'totalPages' => ceil(
                $pagination->getTotalItemCount() / $pagination->getItemNumberPerPage()
            ),
            'items' => $pagination->getItems(),
        ];

        return new JsonResponse(
            $data,
            JsonResponse::HTTP_OK
        );
    }
}
