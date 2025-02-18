<?php

namespace App\Controller;

use App\Entity\Service;
use App\Entity\ServiceRestaurant;
use App\Repository\ServiceRestaurantRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('api/serviceRestaurant', name: 'app_api_serviceRestaurant_')]
final class ServiceRestaurantController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private ServiceRestaurantRepository $repository,
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

        // Vérifier si l'ID du service est présent
        if (!isset($data['service'])) {
            return new JsonResponse(
                ['error' => 'Service ID is required'],
                Response::HTTP_BAD_REQUEST
            );
        }

        // Vérification du format de "heureService" comme une plage horaire "HH:MM - HH:MM"
        if (!preg_match(
            "/^([01]?[0-9]|2[0-3]):[0-5][0-9]( - ([01]?[0-9]|2[0-3]):[0-5][0-9])?$/",
            $data['heureService']
        )) {
            return new JsonResponse(
                [
                    'error'
                    => "Format invalide. Exemple attendu : '09:00' ou '09:00 - 20:00'"
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        // Séparer et convertir les heures en objets DateTime sans date
        if (strpos($data['heureService'], ' - ') !== false) {
            // Format "HH:MM - HH:MM"
            [$heureDebut, $heureFin] = explode(
                ' - ',
                $data['heureService']
            );
            // Créer les objets DateTime pour l'heure, sans la date
            $heureDebut = \DateTime::createFromFormat(
                'H:i',
                trim($heureDebut)
            );
            $heureFin = \DateTime::createFromFormat(
                'H:i',
                trim($heureFin)
            );
        } else {
            // Format "HH:MM" (une seule heure)
            $heureDebut = \DateTime::createFromFormat(
                'H:i',
                $data['heureService']
            );
            // Si une seule heure est fournie, la fin est identique à l'heure de début
            $heureFin = $heureDebut;
        }

        if (!$heureDebut || !$heureFin) {
            return new JsonResponse(
                ['error' => "Format d'heure invalide"],
                Response::HTTP_BAD_REQUEST
            );
        }

        // Récupérer le Service en base
        $service = $this->manager
            ->getRepository(Service::class)
            ->find($data['service']);

        if (!$service) {
            return new JsonResponse(
                ['error' => 'Service non trouvé'],
                Response::HTTP_NOT_FOUND
            );
        }

        // Désérialisation de l'objet ServiceRestaurant
        $serviceRestaurant = $this->serializer
            ->deserialize(
                $request->getContent(),
                ServiceRestaurant::class,
                'json'
            );

        // Associer le Service récupéré
        $serviceRestaurant->setService($service);

        $serviceRestaurant->setHeureDebut($heureDebut);
        $serviceRestaurant->setHeureFin($heureFin);

        // Assigner les valeurs converties
        $serviceRestaurant->setService($service);

        $serviceRestaurant->setCreatedAt(new \DateTimeImmutable());

        // Validation
        $errors = $validator->validate($serviceRestaurant);
        if (count($errors) > 0) {
            return new JsonResponse(
                (string) $errors,
                Response::HTTP_BAD_REQUEST
            );
        }

        $this->manager->persist($serviceRestaurant);
        $this->manager->flush();

        $responseData = $this->serializer
            ->serialize(
                $serviceRestaurant,
                'json',
                ['groups' => 'service_restaurant:read']
            );

        // Convertir les heures en chaîne formatée sans la date
        $serviceRestaurantArray = json_decode(
            $responseData,
            true
        );

        // Retirer les champs heureDebut et heureFin pour ne garder que 'heureService'
        unset($serviceRestaurantArray['heureDebut']);
        unset($serviceRestaurantArray['heureFin']);

        // Ajouter l'heure combinée
        $serviceRestaurantArray['heureService'] = $serviceRestaurant
            ->getHeureDebut()
            ->format('H:i') . ' - ' . $serviceRestaurant
            ->getHeureFin()->format('H:i');

        // Ajouter createdAt uniquement s'il n'est pas null
        if ($serviceRestaurant->getCreatedAt()) {
            $serviceRestaurantArray['createdAt'] = $serviceRestaurant
                ->getCreatedAt()
                ->format('d-m-Y H:i:s');
        }

        // Supprimer updatedAt s'il est null
        if ($serviceRestaurant->getUpdatedAt()) {
            $serviceRestaurantArray['updatedAt'] = $serviceRestaurant
                ->getUpdatedAt()
                ->format('d-m-Y H:i:s');
        } else {
            unset($serviceRestaurantArray['updatedAt']);
        }

        $location = $this->urlGenerator->generate(
            'app_api_serviceRestaurant_show',
            ['id' => $serviceRestaurant->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return new JsonResponse(
            $serviceRestaurantArray,
            Response::HTTP_CREATED,
            ["location" => $location]
        );
    }

    #[Route('/{id}', name: 'show', methods: 'GET')]
    public function show(int $id): JsonResponse
    {
        $serviceRestaurant = $this->repository->findOneBy(['id' => $id]);

        if ($serviceRestaurant) {
            // Sérialisation des données du ServiceRestaurant, mais sans les champs heureDebut et heureFin
            $responseData = $this->serializer
                ->serialize(
                    $serviceRestaurant,
                    'json',
                    ['groups' => 'service_restaurant:read']
                );

            // Convertir les données sérialisées en tableau
            $serviceRestaurantArray = json_decode(
                $responseData,
                true
            );

            // Créer 'heureService' en combinant heureDebut et heureFin
            $heureService = $serviceRestaurant
                ->getHeureDebut()
                ->format('H:i') . ' - ' . $serviceRestaurant
                ->getHeureFin()
                ->format('H:i');

            // Ajouter 'heureService' et retirer 'heureDebut' et 'heureFin'
            $serviceRestaurantArray['heureService'] = $heureService;
            unset($serviceRestaurantArray['heureDebut']);
            unset($serviceRestaurantArray['heureFin']);

            // Ajouter createdAt uniquement s'il n'est pas null
            if ($serviceRestaurant->getCreatedAt()) {
                $serviceRestaurantArray['createdAt'] = $serviceRestaurant
                    ->getCreatedAt()
                    ->format('d-m-Y H:i:s');
            }

            // Supprimer updatedAt s'il est null
            if ($serviceRestaurant->getUpdatedAt()) {
                $serviceRestaurantArray['updatedAt'] = $serviceRestaurant
                    ->getUpdatedAt()
                    ->format('d-m-Y H:i:s');
            } else {
                unset($serviceRestaurantArray['updatedAt']);
            }

            return new JsonResponse(
                $serviceRestaurantArray,
                Response::HTTP_OK
            );
        }

        return new JsonResponse(
            ['error' => 'Service restaurant non trouvé'],
            Response::HTTP_NOT_FOUND
        );
    }

    #[Route('/{id}', name: 'edit', methods: 'PUT')]
    public function edit(
        int $id,
        Request $request,
        ValidatorInterface $validator
    ): JsonResponse {
        $serviceRestaurant = $this->repository->findOneBy(['id' => $id]);

        // Vérification si l'utilisateur a l'un des rôles requis
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_EMPLOYE')) {
            return new JsonResponse(
                ['message' => 'Accès réfusé'],
                Response::HTTP_FORBIDDEN
            );
        }

        if (!$serviceRestaurant) {
            return new JsonResponse(
                ['error' => "Service restaurant n'est pas trouvé"],
                Response::HTTP_NOT_FOUND
            );
        }

        $data = json_decode(
            $request->getContent(),
            true
        );

        if (!isset($data['service'])) {
            return new JsonResponse(
                ['error' => "Le champ 'service' est obligatoire"],
                Response::HTTP_BAD_REQUEST
            );
        }

        // Vérification du format de l'heure
        if (!preg_match(
            "/^([01]?[0-9]|2[0-3]):[0-5][0-9] - ([01]?[0-9]|2[0-3]):[0-5][0-9]$/",
            $data['heureService']
        )) {
            return new JsonResponse(
                [
                    'error' =>
                    "Format invalide. Exemple attendu : '09:00 - 20:00'"
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        // Récupérer l'entité Service existante
        $service = $this->manager
            ->getRepository(Service::class)
            ->find($data['service']);

        if (!$service) {
            return new JsonResponse(
                [
                    'error' => "Le service avec l'ID {$data['service']} n'existe pas"
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        try {
            $this->serializer->deserialize(
                $request->getContent(),
                ServiceRestaurant::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $serviceRestaurant]
            );
        } catch (\Exception $e) {
            return new JsonResponse(
                ['error' => "Données invalides : " . $e->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
        }

        // Assigner le service existant
        $serviceRestaurant->setService($service);

        // Mettre à jour les heures en séparant heureDebut et heureFin
        [$heureDebut, $heureFin] = explode(' - ', $data['heureService']);
        $serviceRestaurant->setHeureDebut(\DateTime::createFromFormat(
            'H:i',
            trim($heureDebut)
        ));
        $serviceRestaurant->setHeureFin(\DateTime::createFromFormat(
            'H:i',
            trim($heureFin)
        ));

        // Validation des données
        $errors = $validator->validate($serviceRestaurant);
        if (count($errors) > 0) {
            return new JsonResponse(
                ['errors' => (string) $errors],
                Response::HTTP_BAD_REQUEST
            );
        }

        $serviceRestaurant->setUpdatedAt(new DateTimeImmutable());

        $this->manager->flush();

        // Sérialiser l'entité
        $modify = $this->serializer
            ->serialize(
                $serviceRestaurant,
                'json',
                ['groups' => 'service_restaurant:read']
            );

        // Transformer en tableau pour manipulation
        $serviceRestaurantArray = json_decode($modify, true);

        // Ajouter heureService et supprimer heureDebut et heureFin
        $serviceRestaurantArray['heureService'] = $serviceRestaurant
            ->getHeureDebut()->format('H:i') . ' - ' . $serviceRestaurant
            ->getHeureFin()
            ->format('H:i');
        unset($serviceRestaurantArray['heureDebut']);
        unset($serviceRestaurantArray['heureFin']);

        // Ajouter createdAt uniquement s'il n'est pas null
        if ($serviceRestaurant->getCreatedAt()) {
            $serviceRestaurantArray['createdAt'] = $serviceRestaurant
                ->getCreatedAt()
                ->format('d-m-Y H:i:s');
        }

        // Supprimer updatedAt s'il est null
        if ($serviceRestaurant->getUpdatedAt()) {
            $serviceRestaurantArray['updatedAt'] = $serviceRestaurant
                ->getUpdatedAt()
                ->format('d-m-Y H:i:s');
        } else {
            unset($serviceRestaurantArray['updatedAt']);
        }

        return new JsonResponse(
            $serviceRestaurantArray,
            Response::HTTP_OK
        );
    }

    #[Route('/{id}', name: 'delete', methods: 'DELETE')]
    public function delete(int $id): JsonResponse
    {
        $serviceRestaurant = $this->repository->findOneBy(['id' => $id]);

        // Vérification si l'utilisateur a l'un des rôles requis
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_EMPLOYE')) {
            return new JsonResponse(
                ['message' => 'Accès réfusé'],
                Response::HTTP_FORBIDDEN
            );
        }

        if ($serviceRestaurant) {
            $this->manager->remove($serviceRestaurant);
            $this->manager->flush();

            return new JsonResponse(
                ['message' => 'Service restraurant supprimer avec succès'],
                Response::HTTP_OK
            );
        }

        return new JsonResponse(
            ['error' => 'Service restaurant non trouvé'],
            Response::HTTP_NOT_FOUND
        );
    }
}
