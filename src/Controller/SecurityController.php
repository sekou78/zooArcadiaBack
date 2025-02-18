<?php

namespace App\Controller;

use App\Entity\Animal;
use App\Entity\User;
use App\Service\ConsultationService;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Attribute\{CurrentUser, IsGranted};
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use OpenApi\Attributes as OA;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

#[Route('/api', name: 'app_api_')]
class SecurityController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private SerializerInterface $serializer,
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    #[Route('/registration', name: 'registration', methods: 'POST')]
    #[OA\Post(
        path: "/api/registration",
        summary: "Inscription d'un Administrateur",
        requestBody: new OA\RequestBody(
            required: true,
            description: "Admin à inscrire",
            content: new OA\MediaType(
                mediaType: "application/json",
                schema: new OA\Schema(
                    type: "object",
                    required: ["email", "password", "roles"],
                    properties: [
                        new OA\Property(
                            property: "email",
                            type: "string",
                            format: "email",
                            example: "adresse@email.com"
                        ),
                        new OA\Property(
                            property: "password",
                            type: "string",
                            format: "password",
                            example: "Azerty$1"
                        ),
                        new OA\Property(
                            property: "roles",
                            type: "array",
                            items: new OA\Items(
                                type: "string",
                                example: "ROLE_ADMIN"
                            )
                        ),
                        new OA\Property(
                            property: "username",
                            type: "string",
                            example: "Dinga"
                        ),
                        new OA\Property(
                            property: "nom",
                            type: "string",
                            example: "Fath"
                        ),
                        new OA\Property(
                            property: "prenom",
                            type: "string",
                            example: "Alpha"
                        )
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Administrateur inscrit avec succès',
                content: new OA\MediaType(
                    mediaType: "application/json",
                    schema: new OA\Schema(
                        type: "object",
                        properties: [
                            new OA\Property(
                                property: "user",
                                type: "string",
                                example: "Mail de connexions"
                            ),
                            new OA\Property(
                                property: "apiToken",
                                type: "string",
                                example: "31a023e212f116124a36af14ea0c1c3806eb9378"
                            ),
                            new OA\Property(
                                property: "roles",
                                type: "array",
                                items: new OA\Items(
                                    type: "string",
                                    example: "ROLE_ADMIN"
                                )
                            ),
                            new OA\Property(
                                property: "username",
                                type: "string",
                                example: "Dinga"
                            ),
                            new OA\Property(
                                property: "nom",
                                type: "string",
                                example: "Fath"
                            ),
                            new OA\Property(
                                property: "prenom",
                                type: "string",
                                example: "Alpha"
                            ),
                            new OA\Property(
                                property: "createdAt",
                                type: "string",
                                format: "date-time"
                            )
                        ]
                    )
                )
            )
        ]
    )]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        ValidatorInterface $validator,
        LoggerInterface $logger
    ): JsonResponse {
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
                $errorMessages[$error->getPropertyPath()]
                    =
                    $error->getMessage();
            }
            return new JsonResponse(
                ['errors' => $errorMessages],
                Response::HTTP_BAD_REQUEST
            );
        }

        // Vérification de l'e-mail dans la base de donnée
        $existingUser = $this->manager
            ->getRepository(User::class)
            ->findOneBy(
                ['email' => $user->getEmail()]
            );
        if ($existingUser) {
            return new JsonResponse(
                ['error' => 'Email déjà utilisé'],
                Response::HTTP_BAD_REQUEST
            );
        }

        // Si l'utilisateur tente de créer un administrateur, vérifiez s'il existe déjà un administrateur
        if (in_array(
            "ROLE_ADMIN",
            $user->getRoles()
        )) {
            $existingAdmin = $this->manager
                ->getRepository(User::class)
                ->findOneByRole('ROLE_ADMIN');
            if ($existingAdmin) {
                return new JsonResponse(
                    ['error' => 'Un compte administrateur existe déjà'],
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
            ['email' => substr(
                $user->getEmail(),
                0,
                3
            ) . '***']
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

    #[Route(
        '/admin/create-user',
        name: 'admin_create_user',
        methods: 'POST'
    )]
    #[IsGranted('ROLE_ADMIN')]
    #[OA\Post(
        path: "/api/admin/create-user",
        summary: "Inscription d'un Utilisateur par Administrateur",
        requestBody: new OA\RequestBody(
            required: true,
            description: "Utilisateur à inscrire par Administrateur",
            content: new OA\MediaType(
                mediaType: "application/json",
                schema: new OA\Schema(
                    type: "object",
                    required: ["email", "password", "roles"],
                    properties: [
                        new OA\Property(
                            property: "email",
                            type: "string",
                            format: "email",
                            example: "adresse@email.com"
                        ),
                        new OA\Property(
                            property: "username",
                            type: "string",
                            example: "Dinga"
                        ),
                        new OA\Property(
                            property: "nom",
                            type: "string",
                            example: "Fath"
                        ),
                        new OA\Property(
                            property: "prenom",
                            type: "string",
                            example: "Alpha"
                        ),
                        new OA\Property(
                            property: "roles",
                            type: "array",
                            items: new OA\Items(
                                type: "string",
                                example: ["ROLE_VETERINAIRE"]
                            ),
                            example: ["ROLE_EMPLOYE"]
                        ),
                        new OA\Property(
                            property: "password",
                            type: "string",
                            format: "password",
                            example: "Azerty$1"
                        ),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Utilisateur inscrit avec succès',
                content: new OA\MediaType(
                    mediaType: "application/json",
                    schema: new OA\Schema(
                        type: "object",
                        properties: [
                            new OA\Property(
                                property: "user",
                                type: "string",
                                example: "Mail de connexions"
                            ),
                            new OA\Property(
                                property: "apiToken",
                                type: "string",
                                example: "31a023e212f116124a36af14ea0c1c3806eb9378"
                            ),
                            new OA\Property(
                                property: "roles",
                                type: "array",
                                items: new OA\Items(
                                    type: "string",
                                    example: ["ROLE_VETERINAIRE"]
                                ),
                                example: ["ROLE_EMPLOYE"]
                            ),
                            new OA\Property(
                                property: "username",
                                type: "string",
                                example: "Dinga"
                            ),
                            new OA\Property(
                                property: "nom",
                                type: "string",
                                example: "Fath"
                            ),
                            new OA\Property(
                                property: "prenom",
                                type: "string",
                                example: "Alpha"
                            ),
                            new OA\Property(
                                property: "createdAt",
                                type: "string",
                                format: "date-time"
                            )
                        ]
                    )
                )
            )
        ]
    )]
    public function createUser(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        ValidatorInterface $validator,
        LoggerInterface $logger
    ): JsonResponse {
        // Désérialisation de l'utilisateur depuis le contenu de la requête
        $user = $this->serializer
            ->deserialize(
                $request->getContent(),
                User::class,
                'json'
            );

        // Validation des données
        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()]
                    =
                    $error->getMessage();
            }
            return new JsonResponse(
                ['errors' => $errorMessages],
                Response::HTTP_BAD_REQUEST
            );
        }

        // Vérification de l'existence d'un utilisateur avec cet email
        $existingUser = $this->manager
            ->getRepository(User::class)
            ->findOneBy(
                ['email' => $user->getEmail()]
            );
        if ($existingUser) {
            return new JsonResponse(
                ['error' => 'Cet email est déjà utlisé'],
                Response::HTTP_BAD_REQUEST
            );
        }

        // Si l'utilisateur tente de créer un administrateur, vérifiez s'il existe déjà un administrateur
        if (in_array("ROLE_ADMIN", $user->getRoles())) {
            $existingAdmin = $this->manager
                ->getRepository(User::class)
                ->findOneByRole('ROLE_ADMIN');
            if ($existingAdmin) {
                return new JsonResponse(
                    ['error' => 'Un compte administrateur existe déjà'],
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
            ['email' => substr(
                $user->getEmail(),
                0,
                3
            ) . '***']
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

    #[Route('/login', name: 'login', methods: 'POST')]
    #[OA\Post(
        path: "/api/login",
        summary: "Connexion d'un Utilisateur",
        requestBody: new OA\RequestBody(
            required: true,
            description: "Données de l'utilisateur pour se connecter",
            content: new OA\MediaType(
                mediaType: "application/json",
                schema: new OA\Schema(
                    type: "object",
                    required: ["username", "password"],
                    properties: [
                        new OA\Property(
                            property: "username",
                            type: "string",
                            example: "adresse@email.com"
                        ),
                        new OA\Property(
                            property: "password",
                            type: "string",
                            example: "Azerty$1"
                        )
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Connexion reussie",
                content: new OA\MediaType(
                    mediaType: "application/json",
                    schema: new OA\Schema(
                        type: "object",
                        properties: [
                            new OA\Property(
                                property: "user",
                                type: "string",
                                example: "Mail de connexions"
                            ),
                            new OA\Property(
                                property: "apiToken",
                                type: "string",
                                example: "31a023e212f116124a36af14ea0c1c3806eb9378"
                            ),
                            new OA\Property(
                                property: "roles",
                                type: "array",
                                items: new OA\Items(
                                    type: "string",
                                    example: ["ROLE_VETERINAIRE"]
                                ),
                                example: ["ROLE_EMPLOYE"]
                            )
                        ]
                    )
                )
            )
        ]
    )]
    public function login(
        #[CurrentUser] ?User $user,
        LoggerInterface $logger
    ): JsonResponse {
        if (null === $user) {
            $logger->warning(
                'Un identifiants manquants'
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
    #[OA\Get(
        path: "/api/account/me",
        summary: "Les informations de l'objet User",
        responses: [
            new OA\Response(
                response: 201,
                description: "Les champs de l'utilisateur",
                content: new OA\MediaType(
                    mediaType: "application/json",
                    schema: new OA\Schema(
                        type: "object",
                        properties: [
                            new OA\Property(
                                property: "user",
                                type: "string",
                                example: "Mail de connexions"
                            ),
                            new OA\Property(
                                property: "apiToken",
                                type: "string",
                                example: "31a023e212f116124a36af14ea0c1c3806eb9378"
                            ),
                            new OA\Property(
                                property: "roles",
                                type: "array",
                                items: new OA\Items(
                                    type: "string",
                                    example: ["ROLE_VETERINAIRE"]
                                ),
                                example: ["ROLE_EMPLOYE"]
                            ),
                            new OA\Property(
                                property: "username",
                                type: "string",
                                example: "Dinga"
                            ),
                            new OA\Property(
                                property: "nom",
                                type: "string",
                                example: "Fath"
                            ),
                            new OA\Property(
                                property: "prenom",
                                type: "string",
                                example: "Alpha"
                            )
                        ]
                    )
                )
            )
        ]
    )]
    public function me(): JsonResponse
    {
        $user = $this->getUser();

        $responseData = $this->serializer
            ->serialize(
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
    #[OA\Put(
        path: "/api/account/edit",
        summary: "Modifier son compte",
        requestBody: new OA\RequestBody(
            required: true,
            description: "Données à mettre à jour",
            content: new OA\MediaType(
                mediaType: "application/json",
                schema: new OA\Schema(
                    type: "object",
                    properties: [
                        new OA\Property(
                            property: "email",
                            type: "string",
                            format: "email",
                            example: "adresse@email.com"
                        ),
                        new OA\Property(
                            property: "username",
                            type: "string",
                            example: "Dinga"
                        ),
                        new OA\Property(
                            property: "nom",
                            type: "string",
                            example: "Fath"
                        ),
                        new OA\Property(
                            property: "prenom",
                            type: "string",
                            example: "Alpha"
                        ),
                        new OA\Property(
                            property: "password",
                            type: "string",
                            format: "password",
                            example: "Azerty$1"
                        )
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Utilisateur modifié avec succès",
                content: new OA\MediaType(
                    mediaType: "application/json",
                    schema: new OA\Schema(
                        type: "object",
                        properties: [
                            new OA\Property(
                                property: "user",
                                type: "string",
                                example: "Mail de connexions"
                            ),
                            new OA\Property(
                                property: "apiToken",
                                type: "string",
                                example: "31a023e212f116124a36af14ea0c1c3806eb9378"
                            ),
                            new OA\Property(
                                property: "roles",
                                type: "array",
                                items: new OA\Items(
                                    type: "string",
                                    example: ["ROLE_VETERINAIRE"]
                                ),
                                example: ["ROLE_EMPLOYE"]
                            ),
                            new OA\Property(
                                property: "username",
                                type: "string",
                                example: "Dinga"
                            ),
                            new OA\Property(
                                property: "nom",
                                type: "string",
                                example: "Fath"
                            ),
                            new OA\Property(
                                property: "prenom",
                                type: "string",
                                example: "Alpha"
                            ),
                            new OA\Property(
                                property: "updatedAt",
                                type: "string",
                                format: "date-time"
                            )
                        ]
                    )
                )
            )
        ]
    )]
    public function edit(
        Request $request,
        LoggerInterface $logger
    ): JsonResponse {
        //Désérialisation des données de la requête pour mettre à jour l'utilisateur
        $user = $this->serializer
            ->deserialize(
                $request->getContent(),
                User::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $this->getUser()],
            );
        $user->setUpdatedAt(new \DateTimeImmutable());

        // Vérification si l'utilisateur tente de modifier ses rôles
        $data = $request->toArray();
        if (isset($data['roles']) && !$this->isGranted('ROLE_ADMIN')) {
            return new JsonResponse(
                ['error' => 'You cannot modify roles'],
                Response::HTTP_FORBIDDEN
            );
        }

        // Hachage du mot de passe si modifié
        if (isset($request->toArray()['password'])) {
            $user->setPassword(
                $this->passwordHasher
                    ->hashPassword(
                        $user,
                        $user->getPassword()
                    )
            );
        }

        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            $existingAdmin = $this->manager
                ->getRepository(User::class)
                ->findOneByRole('ROLE_ADMIN');
            if ($existingAdmin) {
                return new JsonResponse(
                    ['error' => 'Un compte administrateur existe déjà.'],
                    Response::HTTP_FORBIDDEN
                );
            }
        }

        $this->manager->flush();

        $logger->info(
            'Nouvelle utilisateur enregistrer',
            ['email' => $user->getEmail()]
        );

        // Retourner la réponse JSON avec les informations mises à jour
        $responseData = $this->serializer
            ->serialize(
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

    #[Route(
        '/admin/reset-password/{username}',
        name: 'admin_reset_password',
        methods: 'POST'
    )]
    #[IsGranted('ROLE_ADMIN')]
    #[OA\Post(
        path: "/api/admin/reset-password/{username}",
        summary: "Réinitialisation du mot de passe d'un utilisateur",
        description: "Réinitialisation du mot de passe d'un utilisateur
            et de lui envoyer un nouveau mot de passe par email",
        parameters: [
            new OA\Parameter(
                name: "username",
                in: "path",
                required: true,
                description: "Username de l'utilisateur dont 
                    le mot de passe doit être réinitialisé",
                schema: new OA\Schema(
                    type: "string",
                    example: "Dinga"
                )
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Le mot de passe a été réinitialisé 
                    et envoyé par email",
                content: new OA\MediaType(
                    mediaType: "application/json",
                    schema: new OA\Schema(
                        type: "object",
                        properties: [
                            new OA\Property(
                                property: "message",
                                type: "string",
                                example: "Le mot de passe a été réinitialisé et envoyé par email"
                            )
                        ]
                    )
                )
            ),
            new OA\Response(
                response: 404,
                description: "Utilisateur non trouvé",
                content: new OA\MediaType(
                    mediaType: "application/json",
                    schema: new OA\Schema(
                        type: "object",
                        properties: [
                            new OA\Property(
                                property: "error",
                                type: "string",
                                example: "Cet utilisateur n'existe pas"
                            )
                        ]
                    )
                )
            ),
            new OA\Response(
                response: 403,
                description: "Accès refusé",
                content: new OA\MediaType(
                    mediaType: "application/json",
                    schema: new OA\Schema(
                        type: "object",
                        properties: [
                            new OA\Property(
                                property: "error",
                                type: "string",
                                example: "Accès refusé"
                            )
                        ]
                    )
                )
            )
        ]
    )]
    public function resetPassword(
        string $username,
        UserPasswordHasherInterface $passwordHasher,
        LoggerInterface $logger,
        MailerInterface $mailer // Service de mail pour notifier l'utilisateur
    ): JsonResponse {
        // Trouver l'utilisateur par son nom d'utilisateur (username)
        $user = $this->manager
            ->getRepository(User::class)
            ->findOneBy(
                ['username' => $username]
            );

        if (!$user) {
            return new JsonResponse(
                ['error' => 'Utilisateur non trouvé'],
                Response::HTTP_NOT_FOUND
            );
        }

        // Générer un nouveau mot de passe aléatoire
        $newPassword = bin2hex(random_bytes(8));
        $user->setPassword(
            $passwordHasher->hashPassword(
                $user,
                $newPassword
            )
        );

        // Sauvegarder l'utilisateur avec le nouveau mot de passe
        $this->manager->persist($user);
        $this->manager->flush();

        // Créer l'email
        $email = (new Email())
            ->from('no-reply@example.com')  // L'adresse d'expéditeur
            ->to($user->getEmail())        // L'adresse du destinataire
            ->subject('Votre mot de passe a été réinitialisé') // Sujet de l'email
            ->text('Votre nouveau mot de passe est : ' . $newPassword); // Contenu texte

        // Envoyer l'email
        $mailer->send($email);

        // Log l'action
        $logger->info(
            "Mot de passe réinitialisé pour l'utilisateur",
            [
                'username' => $username
            ]
        );

        return new JsonResponse(
            [
                "message" => "Le mot de passe a été réinitialisé 
                                et envoyé par email"
            ],
            Response::HTTP_OK
        );
    }

    //le Dashboard pour visualiser quels animaux qui plaisent le plus
    #[Route(
        '/admin/dashboardAnimal',
        name: 'dashboardAnimal',
        methods: 'GET'
    )]
    #[IsGranted('ROLE_ADMIN')]
    #[OA\Get(
        path: "/api/admin/dashboardAnimal",
        summary: "Visualiser les animaux les plus populaires",
        description: "Retourne la liste des animaux triés en fonction du nombre de consultations",
        responses: [
            new OA\Response(
                response: 200,
                description: "Liste des animaux triée par popularité",
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(
                                property: 'nom',
                                type: 'string',
                                description: "Nom de l'animal"
                            ),
                            new OA\Property(
                                property: 'consultations',
                                type: 'integer',
                                description: "Nombre de consultations"
                            )
                        ]
                    )
                )
            ),
            new OA\Response(
                response: 403,
                description: "Accès refusé"
            ),
            new OA\Response(
                response: 500,
                description: "Erreur serveur"
            )
        ]
    )]
    public function dashboardAnimal(
        ConsultationService $consultationService
    ): JsonResponse {
        // Récupérer tous les animaux
        $animaux = $this->manager
            ->getRepository(Animal::class)
            ->findAll();

        // Trier les animaux par nombre de consultations
        usort($animaux, function ($a, $b) use ($consultationService) {
            $consultationA = $consultationService
                ->getConsultationCount(
                    $a->getFirstname()
                );
            $consultationB = $consultationService
                ->getConsultationCount(
                    $b->getFirstname()
                );
            return $consultationB - $consultationA; // Tri décroissant
        });

        // Structurer les données à retourner
        $data = array_map(function ($animal) use ($consultationService) {
            return [
                'nom' => $animal->getFirstname(),
                'consultations' => $consultationService
                    ->getConsultationCount(
                        $animal->getFirstname()
                    )
            ];
        }, $animaux);

        return new JsonResponse(
            $data,
            JsonResponse::HTTP_OK
        );
    }
}
