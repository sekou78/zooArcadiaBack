<?php

namespace App\Controller;

use App\Entity\Role;
use App\Entity\User;
use App\Repository\RoleRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\SecurityBundle\Security;

#[Route('api/role', name: 'app_api_role_')]
final class RoleController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private RoleRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator
    ) {}

    #[Route(name: 'new', methods: 'POST')]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request): JsonResponse
    {
        $role = $this->serializer->deserialize($request->getContent(), Role::class, 'json');
        $role->setCreatedAt(new DateTimeImmutable());

        $this->manager->persist($role);
        $this->manager->flush();

        $responseData = $this->serializer->serialize($role, 'json');
        $location = $this->urlGenerator->generate(
            'app_api_role_show',
            ['id' => $role->getId()],
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
    public function show(int $id): JsonResponse
    {
        $role = $this->repository->findOneBy(['id' => $id]);

        if ($role) {
            $responseData = $this->serializer->serialize($role, 'json');

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

    #[Route("/assign-role", name: "assign_role", methods: "PUT")]
    #[IsGranted('ROLE_ADMIN')]
    public function assignRoleToUser(
        Request $request,
        EntityManagerInterface $manager,
        Security $security
    ): JsonResponse {
        // Vérifier que l'utilisateur actuel est un administrateur (juste une sécurité supplémentaire)
        if (!$security->isGranted('ROLE_ADMIN')) {
            return new JsonResponse(['message' => 'Accès refusé'], JsonResponse::HTTP_FORBIDDEN);
        }

        // Récupérer le contenu JSON
        $data = json_decode(
            $request->getContent(),
            true
        );

        if (!$data || !isset($data['email'], $data['role_id'])) {
            return new JsonResponse(
                [
                    'message' => 'Données invalides. Un email et un role_id sont requis.'
                ],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $email = $data['email'];
        $roleId = $data['role_id'];

        // Trouver l'utilisateur via son email
        $user = $manager->getRepository(User::class)->findOneBy(['email' => $email]);
        $role = $manager->getRepository(Role::class)->find($roleId);

        if (!$user) {
            return new JsonResponse(
                ['message' => 'Utilisateur non trouvé'],
                JsonResponse::HTTP_NOT_FOUND
            );
        }

        if (!$role) {
            return new JsonResponse(
                ['message' => 'Rôle non trouvé'],
                JsonResponse::HTTP_NOT_FOUND
            );
        }

        // Si l'utilisateur tente de créer un administrateur, vérifiez s'il existe déjà un administrateur
        if ($role->getLabel() === 'ROLE_ADMIN') {
            $existingAdmin = $manager->getRepository(User::class)->findOneBy(['role' => $role]);

            // Si un administrateur existe déjà
            if ($existingAdmin) {
                return new JsonResponse(
                    [
                        'error' => 'Un administrateur existe déjà. Un seul administrateur peut être assigné.'
                    ],
                    JsonResponse::HTTP_FORBIDDEN
                );
            }
        }

        // Affecter le rôle à l'utilisateur
        $user->setRole($role);

        // Mettre à jour la collection 'users' du rôle
        $role->addUser($user); // Ajoute l'utilisateur à la collection 'users' du rôle

        // Sauvegarder les modifications dans la base de données
        $this->manager->flush();

        return new JsonResponse([
            'message' => "Rôle attribué à l'utilisateur avec succès.",
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'role' => $role->getLabel(),
            ],
            'role' => [
                'id' => $role->getId(),
                'label' => $role->getLabel(),
                'users_count' => count($role->getUsers()), // Nombre d'utilisateurs associés au rôle
            ]
        ], JsonResponse::HTTP_OK);
    }

    //Suppression d'un role en BDD via son label
    #[Route("/delete-role-by-label", name: "delete_role_by_label", methods: "DELETE")]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteRoleByLabel(Request $request, EntityManagerInterface $manager): JsonResponse
    {
        // Récupérer le contenu JSON
        $data = json_decode($request->getContent(), true);

        if (!$data || !isset($data['label'])) {
            return new JsonResponse(
                ['message' => 'Le label du rôle est requis.'],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $label = $data['label'];

        // Trouver le rôle par son label
        $role = $manager->getRepository(Role::class)->findOneBy(['label' => $label]);

        if (!$role) {
            return new JsonResponse(
                ['message' => 'Rôle non trouvé'],
                JsonResponse::HTTP_NOT_FOUND
            );
        }

        // Supprimer le rôle
        $manager->remove($role);
        $manager->flush();

        return new JsonResponse(
            ['message' => 'Rôle supprimé avec succès'],
            JsonResponse::HTTP_OK
        );
    }

    //Suppression du role attribuer à un utilisateur via son email
    #[Route("/delete-role", name: "delete_role_by_email", methods: "DELETE")]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteRoleByEmail(
        Request $request,
        EntityManagerInterface $manager
    ): JsonResponse {
        // Récupérer le contenu JSON
        $data = json_decode($request->getContent(), true);

        if (!$data || !isset($data['email'])) {
            return new JsonResponse(
                ['message' => 'L\'email est requis.'],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        // Trouver l'utilisateur par email
        $email = $data['email'];
        $user = $manager->getRepository(User::class)->findOneBy(['email' => $email]);

        if (!$user) {
            return new JsonResponse(
                ['message' => 'Utilisateur non trouvé'],
                JsonResponse::HTTP_NOT_FOUND
            );
        }

        // Vérifier si l'utilisateur a un rôle attribué
        $role = $user->getRole();
        if (!$role) {
            return new JsonResponse(
                ['message' => 'L\'utilisateur n\'a pas de rôle attribué'],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        // Retirer le rôle de l'utilisateur
        $user->setRole(null);  // Retirer le rôle de l'utilisateur

        // Enregistrer les changements dans la base de données
        $manager->flush();

        return new JsonResponse(
            ['message' => 'Rôle retiré avec succès de l\'utilisateur.'],
            JsonResponse::HTTP_OK
        );
    }
}
