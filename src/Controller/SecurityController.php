<?php

namespace App\Controller;

use App\Entity\User;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api', name: 'app_api_')]
class SecurityController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private SerializerInterface $serializer,
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    #[Route('/registration', name: 'registration', methods: 'POST')]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $user = $this->serializer->deserialize(
            $request->getContent(),
            User::class,
            'json'
        );
        $user->setPassword(
            $passwordHasher->hashPassword(
                $user,
                $user->getPassword()
            )
        );
        $user->setCreatedAt(new DateTimeImmutable());

        $this->manager->persist($user);
        $this->manager->flush();

        return new JsonResponse(
            [
                'user'  => $user->getUserIdentifier(),
                'apiToken' => $user->getApiToken(),
                'roles' => $user->getRoles()
            ],
            Response::HTTP_CREATED
        );
    }

    #[Route('/login', name: 'login', methods: 'POST')]
    public function login(#[CurrentUser] ?User $user): JsonResponse
    {
        if (null === $user) {
            return $this->json(
                [
                    'message' => 'missing credentials',
                ],
                Response::HTTP_UNAUTHORIZED
            );
        }

        return new JsonResponse(
            [
                'user'  => $user->getUserIdentifier(),
                'apiToken' => $user->getApiToken(),
                'roles' => $user->getRoles()
            ],
        );
    }

    #[Route('/account/me', name: 'me', methods: 'GET')]
    public function me(): JsonResponse
    {
        $user = $this->getUser();

        $responseData = $this->serializer->serialize($user, 'json');

        return new JsonResponse($responseData, Response::HTTP_OK, [], true);
    }

    #[Route('/account/edit', name: 'edit', methods: 'PUT')]
    public function edit(Request $request): JsonResponse
    {
        $user = $this->serializer->deserialize(
            $request->getContent(),
            User::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $this->getUser()],
        );
        $user->setUpdatedAt(new DateTimeImmutable());

        if (isset($request->toArray()['password'])) {
            $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPassword()));
        }

        $this->manager->flush();

        $responseData = $this->serializer->serialize($user, 'json');

        return new JsonResponse(
            $responseData,
            Response::HTTP_OK,
            [],
            true
        );
    }
}
