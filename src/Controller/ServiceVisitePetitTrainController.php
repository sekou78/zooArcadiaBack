<?php

namespace App\Controller;

use App\Entity\Service;
use App\Entity\ServiceVisitePetitTrain;
use App\Form\ServiceVisitePetitTrainType;
use App\Repository\ServiceVisitePetitTrainRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('api/serviceVisitePetitTrain', name: 'app_api_serviceVisitePetitTrain_')]
final class ServiceVisitePetitTrainController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private ServiceVisitePetitTrainRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator
    ) {}

    #[Route(name: 'new', methods: 'POST')]
    public function new(
        Request $request,
        ValidatorInterface $validator
    ): JsonResponse {
        $data = json_decode(
            $request->getContent(),
            true
        );

        // Vérification si l'utilisateur a l'un des rôles requis
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_EMPLOYE')) {
            return new JsonResponse(
                ['message' => 'Accès réfusé'],
                Response::HTTP_FORBIDDEN
            );
        }

        if (!isset($data['service'])) {
            return new JsonResponse(
                ['error' => "L'ID du service est requis"],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        // Récupérer le Service en base
        $service = $this->manager
            ->getRepository(Service::class)
            ->find($data['service']);
        if (!$service) {
            return new JsonResponse(
                ['error' => "Service non trouvé"],
                JsonResponse::HTTP_NOT_FOUND
            );
        }

        // Désérialisation de l'objet ServiceVisitePetitTrain
        $serviceVisitePetitTrain = $this->serializer
            ->deserialize(
                $request->getContent(),
                ServiceVisitePetitTrain::class,
                'json'
            );

        // Associer le Service récupéré
        $serviceVisitePetitTrain->setService($service);

        $serviceVisitePetitTrain->setCreatedAt(new \DateTimeImmutable());

        // Validation
        $errors = $validator->validate($serviceVisitePetitTrain);
        if (count($errors) > 0) {
            return new JsonResponse(
                ['error' => (string) $errors],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $this->manager->persist($serviceVisitePetitTrain);
        $this->manager->flush();

        // Sérialisation en tableau pour modification
        $responseData = json_decode($this->serializer->serialize(
            $serviceVisitePetitTrain,
            'json',
            ['groups' => 'service_visite_petit_train:read']
        ), true);

        // Ajouter createdAt uniquement s'il n'est pas null
        if ($serviceVisitePetitTrain->getCreatedAt()) {
            $responseData['createdAt'] = $serviceVisitePetitTrain
                ->getCreatedAt()
                ->format('d-m-Y H:i:s');
        }

        // Supprimer updatedAt s'il est null
        if ($serviceVisitePetitTrain->getUpdatedAt()) {
            $responseData['updatedAt'] = $serviceVisitePetitTrain
                ->getUpdatedAt()
                ->format('d-m-Y H:i:s');
        } else {
            unset($responseData['updatedAt']);
        }

        $location = $this->urlGenerator->generate(
            'app_api_serviceVisitePetitTrain_show',
            ['id' => $serviceVisitePetitTrain->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return new JsonResponse(
            $responseData,
            JsonResponse::HTTP_CREATED,
            ["location" => $location]
        );
    }

    #[Route('/{id}', name: 'show', methods: 'GET')]
    public function show(int $id): JsonResponse
    {
        $serviceVisitePetitTrain = $this->repository->findOneBy(['id' => $id]);

        if (!$serviceVisitePetitTrain) {
            return new JsonResponse(
                ['error' => "Service de visite non trouvé"],
                JsonResponse::HTTP_NOT_FOUND
            );
        }

        // Désérialiser en tableau pour modification
        $responseData = json_decode(
            $this->serializer
                ->serialize(
                    $serviceVisitePetitTrain,
                    'json',
                    ['groups' => 'service_visite_petit_train:read']
                ),
            true
        );

        // Ajouter createdAt uniquement s'il n'est pas null
        if ($serviceVisitePetitTrain->getCreatedAt()) {
            $responseData['createdAt'] = $serviceVisitePetitTrain
                ->getCreatedAt()
                ->format('d-m-Y H:i:s');
        }

        // Supprimer updatedAt s'il est null
        if ($serviceVisitePetitTrain->getUpdatedAt()) {
            $responseData['updatedAt'] = $serviceVisitePetitTrain
                ->getUpdatedAt()
                ->format('d-m-Y H:i:s');
        } else {
            unset(
                $responseData['updatedAt']
            );
        }

        return new JsonResponse(
            $responseData,
            JsonResponse::HTTP_OK
        );
    }


    #[Route('/{id}', name: 'edit', methods: 'PUT')]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(
        int $id,
        Request $request,
        ValidatorInterface $validator
    ): JsonResponse {
        $serviceVisitePetitTrain = $this->repository->findOneBy(['id' => $id]);

        // Vérification si l'utilisateur a l'un des rôles requis
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_EMPLOYE')) {
            return new JsonResponse(
                ['message' => 'Accès réfusé'],
                Response::HTTP_FORBIDDEN
            );
        }

        if (!$serviceVisitePetitTrain) {
            return new JsonResponse(
                ['error' => "Service de visite non trouvé"],
                JsonResponse::HTTP_NOT_FOUND
            );
        }

        $data = json_decode(
            $request->getContent(),
            true
        );

        if (!isset($data['service'])) {
            return new JsonResponse(
                ['error' => "Le champ 'service' est obligatoire"],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $service = $this->manager
            ->getRepository(Service::class)
            ->find($data['service']);
        if (!$service) {
            return new JsonResponse(
                ['error' => "Service non trouvé"],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        try {
            $this->serializer
                ->deserialize(
                    $request->getContent(),
                    ServiceVisitePetitTrain::class,
                    'json',
                    [
                        AbstractNormalizer::OBJECT_TO_POPULATE
                        => $serviceVisitePetitTrain
                    ]
                );
        } catch (\Exception $e) {
            return new JsonResponse(
                [
                    'error' =>
                    "Données invalides : " . $e->getMessage()
                ],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $serviceVisitePetitTrain->setService($service);

        $errors = $validator->validate($serviceVisitePetitTrain);
        if (count($errors) > 0) {
            return new JsonResponse(['error' => (string) $errors], JsonResponse::HTTP_BAD_REQUEST);
        }

        $serviceVisitePetitTrain->setUpdatedAt(new DateTimeImmutable());

        $this->manager->flush();

        // Désérialiser en tableau pour modification
        $responseData = json_decode(
            $this->serializer
                ->serialize(
                    $serviceVisitePetitTrain,
                    'json',
                    ['groups' => 'service_visite_petit_train:read']
                ),
            true
        );

        // Ajouter createdAt uniquement s'il n'est pas null
        if ($serviceVisitePetitTrain->getCreatedAt()) {
            $responseData['createdAt'] = $serviceVisitePetitTrain
                ->getCreatedAt()
                ->format('d-m-Y H:i:s');
        }

        // Supprimer updatedAt s'il est null
        if ($serviceVisitePetitTrain->getUpdatedAt()) {
            $responseData['updatedAt'] = $serviceVisitePetitTrain
                ->getUpdatedAt()
                ->format('d-m-Y H:i:s');
        } else {
            unset(
                $responseData['updatedAt']
            );
        }

        return new JsonResponse(
            $responseData,
            JsonResponse::HTTP_OK
        );
    }

    #[Route('/{id}', name: 'delete', methods: 'DELETE')]
    public function delete(int $id): JsonResponse
    {
        $serviceVisitePetitTrain = $this->repository->findOneBy(['id' => $id]);

        // Vérification si l'utilisateur a l'un des rôles requis
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_EMPLOYE')) {
            return new JsonResponse(
                ['message' => 'Accès réfusé'],
                Response::HTTP_FORBIDDEN
            );
        }

        if ($serviceVisitePetitTrain) {
            $this->manager->remove($serviceVisitePetitTrain);
            $this->manager->flush();

            return new JsonResponse(
                [
                    'message' => "Service de visite en Petit Train à été supprimer avec succès"
                ],
                Response::HTTP_OK
            );
        }

        return new JsonResponse(
            ['error' => 'Service de visite en Petit Train non trouvé'],
            Response::HTTP_NOT_FOUND
        );
    }
}
