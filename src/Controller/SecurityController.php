<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Entity\Role;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Http\Attribute\{CurrentUser, IsGranted};
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api', name: 'app_api_')]
class SecurityController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private SerializerInterface $serializer,
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    #[Route('/registration', name: 'registration', methods: 'POST')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        CsrfTokenManagerInterface $csrfTokenManager,
        ValidatorInterface $validator,
        LoggerInterface $logger
    ): JsonResponse {
        //Utiliser un jeton CSRF
        // $csrfToken = $request->headers->get('X-CSRF-TOKEN');
        // if (!$csrfTokenManager->isTokenValid(new CsrfToken('registration', $csrfToken))) {
        //     return new JsonResponse(
        //         ['error' => 'Invalid CSRF token'],
        //         Response::HTTP_FORBIDDEN
        //     );
        // }

        // Désérialisation de l'utilisateur depuis le contenu de la requête
        $user = $this->serializer->deserialize(
            $request->getContent(),
            User::class,
            'json'
        );

        // Validation des données
        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            return new JsonResponse(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        // Vérification de l'e-mail dans la base de donnée
        $existingUser = $this->manager->getRepository(User::class)->findOneBy(
            ['email' => $user->getEmail()]
        );
        if ($existingUser) {
            return new JsonResponse(
                ['error' => 'Email is already in use'],
                Response::HTTP_BAD_REQUEST
            );
        }

        // Si l'utilisateur tente de créer un administrateur, vérifiez s'il existe déjà un administrateur
        if (in_array("ROLE_ADMIN", $user->getRoles())) {
            $existingAdmin = $this->manager->getRepository(User::class)->findOneByRole('ROLE_ADMIN');
            if ($existingAdmin) {
                return new JsonResponse(
                    ['error' => 'An admin account already exists. Only one admin can be created.'],
                    Response::HTTP_FORBIDDEN
                );
            }
        }

        // Hachage du mot de passe
        $user->setPassword(
            $passwordHasher->hashPassword(
                $user,
                $user->getPassword()
            )
        );

        $user->setCreatedAt(new DateTimeImmutable());

        $this->manager->persist($user);
        $this->manager->flush();

        $logger->info(
            'New user registered',
            ['email' => substr($user->getEmail(), 0, 3) . '***']
        );


        return new JsonResponse(
            // ['message' => 'User registered successfully'],
            //Pour le test à supprimer avant production (mise en ligne)
            [
                'user'  => $user->getUserIdentifier(),
                'apiToken' => $user->getApiToken(),
                'roles' => $user->getRoles()
            ],
            Response::HTTP_CREATED
        );
    }

    #[Route('/admin/create-user', name: 'admin_create_user', methods: 'POST')]
    #[IsGranted('ROLE_ADMIN')]
    public function createUser(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        CsrfTokenManagerInterface $csrfTokenManager,
        ValidatorInterface $validator,
        LoggerInterface $logger
    ): JsonResponse {
        //Utiliser un jeton CSRF
        // $csrfToken = $request->headers->get('X-CSRF-TOKEN');
        // if (!$csrfTokenManager->isTokenValid(new CsrfToken('registration', $csrfToken))) {
        //     return new JsonResponse(
        //         ['error' => 'Invalid CSRF token'],
        //         Response::HTTP_FORBIDDEN
        //     );
        // }

        // Désérialisation de l'utilisateur depuis le contenu de la requête
        $user = $this->serializer->deserialize(
            $request->getContent(),
            User::class,
            'json'
        );

        // Validation des données
        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            return new JsonResponse(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        // Vérification de l'e-mail dans la base de donnée
        $existingUser = $this->manager->getRepository(User::class)->findOneBy(
            ['email' => $user->getEmail()]
        );
        if ($existingUser) {
            return new JsonResponse(
                ['error' => 'Email is already in use'],
                Response::HTTP_BAD_REQUEST
            );
        }

        // Si l'utilisateur tente de créer un administrateur, vérifiez s'il existe déjà un administrateur
        if (in_array("ROLE_ADMIN", $user->getRoles())) {
            $existingAdmin = $this->manager->getRepository(User::class)->findOneByRole('ROLE_ADMIN');
            if ($existingAdmin) {
                return new JsonResponse(
                    ['error' => 'An admin account already exists. Only one admin can be created.'],
                    Response::HTTP_FORBIDDEN
                );
            }
        }

        // Hachage du mot de passe
        $user->setPassword(
            $passwordHasher->hashPassword(
                $user,
                $user->getPassword()
            )
        );

        $user->setCreatedAt(new DateTimeImmutable());

        $this->manager->persist($user);
        $this->manager->flush();

        $logger->info(
            'New user registered',
            ['email' => substr($user->getEmail(), 0, 3) . '***']
        );


        return new JsonResponse(
            // ['message' => 'User registered successfully'],
            //Pour le test à supprimer avant production (mise en ligne)
            [
                'user'  => $user->getUserIdentifier(),
                'apiToken' => $user->getApiToken(),
                'roles' => $user->getRoles()
            ],
            Response::HTTP_CREATED
        );
    }

    #[Route('/employee/validate-avis/{avisId}', name: 'employee_validate_avis', methods: 'PUT')]
    #[IsGranted('ROLE_EMPLOYE')]
    public function validateAvis(
        int $avisId,
        EntityManagerInterface $manager
    ): JsonResponse {
        $avis = $manager->getRepository(Avis::class)->find($avisId);

        if (!$avis) {
            return new JsonResponse(
                ['error' => 'Feedback not found'],
                Response::HTTP_NOT_FOUND
            );
        }

        // Valider l'avis du visiteur
        $avis->setVisible(true);
        $manager->flush();

        return new JsonResponse(
            ['message' => 'Feedback validated successfully']
        );
    }

    // #[Route('/veterinarian/reports', name: 'veterinarian_reports', methods: 'GET')]
    // #[IsGranted('ROLE_VETERINAIRE')]
    // public function getVeterinarianReports(): JsonResponse
    // {
    //     $user = $this->getUser();
    //     $reports = $user->getRapportsVeterinaires();

    //     // Serialisation des rapports
    //     $responseData = $this->serializer->serialize(
    //         $reports,
    //         'json'
    //     );

    //     return new JsonResponse($responseData, Response::HTTP_OK, [], true);
    // }

    #[Route('/login', name: 'login', methods: 'POST')]
    public function login(
        #[CurrentUser] ?User $user,
        LoggerInterface $logger
    ): JsonResponse {
        if (null === $user) {
            $logger->warning(
                'Tentative de connexion avec des identifiants manquants.'
            );
            return $this->json(
                [
                    'message' => 'missing credentials',
                ],
                Response::HTTP_UNAUTHORIZED
            );
        }

        $logger->info(
            sprintf(
                'Utilisateur connecté : %s',
                $user->getEmail()
            )
        );
        return new JsonResponse(
            // ['message' => 'User registered successfully'],
            //Pour le test à supprimer avant production (mise en ligne)
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

        $responseData = $this->serializer->serialize(
            $user,
            'json',
            [
                AbstractNormalizer::ATTRIBUTES => [
                    'id',
                    'email',
                    'roles',
                    'username',
                    'nom',
                    'prenom'
                ]
            ]
        );

        return new JsonResponse(
            $responseData,
            Response::HTTP_OK,
            [],
            true
        );
    }

    #[Route('/account/edit', name: 'edit', methods: 'PUT')]
    public function edit(
        Request $request,
        CsrfTokenManagerInterface $csrfTokenManager,
        LoggerInterface $logger
    ): JsonResponse {

        // Récupération et validation du jeton CSRF
        // $csrfToken = $request->headers->get('X-CSRF-TOKEN');
        // if (!$csrfTokenManager->isTokenValid(new CsrfToken('edit_account', $csrfToken))) {
        //     return new JsonResponse(
        //         ['error' => 'Invalid CSRF token'],
        //         Response::HTTP_FORBIDDEN
        //     );
        // }

        //Désérialisation des données de la requête pour mettre à jour l'utilisateur
        $user = $this->serializer->deserialize(
            $request->getContent(),
            User::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $this->getUser()],
        );
        $user->setUpdatedAt(new \DateTimeImmutable());

        $data = $request->toArray();
        if (isset($data['roles']) && !$this->isGranted('ROLE_ADMIN')) {
            return new JsonResponse(
                ['error' => 'You cannot modify roles'],
                Response::HTTP_FORBIDDEN
            );
        }

        if (isset($request->toArray()['password'])) {
            $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPassword()));
        }

        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            $existingAdmin = $this->manager->getRepository(User::class)->findOneByRole('ROLE_ADMIN');
            if ($existingAdmin) {
                return new JsonResponse(
                    ['error' => 'An admin account already exists.'],
                    Response::HTTP_FORBIDDEN
                );
            }
        }

        $this->manager->flush();

        $logger->info('New user registered', ['email' => $user->getEmail()]);

        $responseData = $this->serializer->serialize(
            $user,
            'json',
            [
                AbstractNormalizer::ATTRIBUTES => [
                    'id',
                    'email',
                    'username',
                    'roles'
                ]
            ]
        );

        return new JsonResponse(
            $responseData,
            Response::HTTP_OK,
            [],
            true
        );
    }
}
