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
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

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

    // #[Route('/{id}', name: 'edit', methods: 'PUT')]
    // public function edit(int $id, Request $request): JsonResponse
    // {
    // $role = $this->repository->findOneBy(['id' => $id]);

    // if ($role) {
    //     $role = $this->serializer->deserialize(
    //         $request->getContent(),
    //         role::class,
    //         'json',
    //         [AbstractNormalizer::OBJECT_TO_POPULATE => $role]
    //     );

    //     $role->setUpdatedAt(new DateTimeImmutable());

    //     $this->manager->flush();

    //     $modify = $this->serializer->serialize($role, 'json');

    //     return new JsonResponse(
    //         $modify,
    //         Response::HTTP_OK,
    //         [],
    //         true
    //     );
    // }

    // return new JsonResponse(
    //     null,
    //     Response::HTTP_NOT_FOUND
    // );

    // $user = $this->manager->getRepository(User::class)->find($id);

    // if (!$user) {
    //     return new JsonResponse(
    //         ['message' => 'User not found'],
    //         Response::HTTP_NOT_FOUND
    //     );
    // }

    // // Récupérer le rôle à affecter
    // $role = $this->repository->findOneBy(['label' => $request->get('roles')]);

    // if (!$role) {
    //     return new JsonResponse(
    //         ['message' => 'Role not found'],
    //         Response::HTTP_NOT_FOUND
    //     );
    // }

    // // Affecter le rôle à l'utilisateur
    // $user->setRole($role);
    // $user->setUpdatedAt(new \DateTimeImmutable());

    // $this->manager->flush();

    // $responseData = $this->serializer->serialize($user, 'json');
    // return new JsonResponse(
    //     $responseData,
    //     Response::HTTP_OK,
    //     [],
    //     true
    // );



    #[Route("/assign-role/{userId}/{roleId}", name: "assign_role", methods: "PUT")]
    public function assignRoleToUser(int $userId, int $roleId): JsonResponse
    {
        // Trouver l'utilisateur et le rôle
        $user = $this->manager->getRepository(User::class)->find($userId);
        $role = $this->manager->getRepository(Role::class)->find($roleId);

        if (!$user) {
            return new JsonResponse(['message' => 'Utilisateur non trouvé'], JsonResponse::HTTP_NOT_FOUND);
        }

        if (!$role) {
            return new JsonResponse(['message' => 'Rôle non trouvé'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Affecter le rôle à l'utilisateur
        $user->setRole($role);

        // Mettre à jour la collection 'users' du rôle
        $role->addUser($user); // Ajoute l'utilisateur à la collection 'users' du rôle

        // Sauvegarder les modifications dans la base de données
        $this->manager->flush();

        return new JsonResponse([
            'message' => 'Rôle attribué à l\'utilisateur avec succès.',
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


    #[Route('/{id}', name: 'delete', methods: 'DELETE')]
    public function delete(int $id): JsonResponse
    {
        $role = $this->repository->findOneBy(['id' => $id]);

        if ($role) {
            $this->manager->remove($role);
            $this->manager->flush();

            return new JsonResponse(
                ['message' => 'Role deleted successfully'],
                Response::HTTP_OK
            );
        }

        return new JsonResponse(
            null,
            Response::HTTP_NOT_FOUND
        );
    }
}
