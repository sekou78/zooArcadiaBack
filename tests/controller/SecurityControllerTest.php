<?php

// namespace App\Tests\Controller;

// use App\Entity\User;
// use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
// use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

// class LoginControllerTest extends WebTestCase
// {
//     public function testLoginSuccess(): void
//     {
//         // Créer un client
//         $client = static::createClient();

//         // Accéder au conteneur de services pour obtenir le service PasswordHasher
//         $passwordHasher = self::getContainer()->get(UserPasswordHasherInterface::class);

//         // Créer un utilisateur de test et l'insérer dans la base de données
//         $entityManager = self::getContainer()->get('doctrine')->getManager();

//         $user = new User();
//         $user->setEmail('adressetest@email.com');
//         $user->setRoles(['ROLE_ADMIN']);

//         // Hacher le mot de passe avec PasswordHasherInterface
//         $password = 'Azerty$1';
//         $hashedPassword = $passwordHasher->hash($password);
//         $user->setPassword($hashedPassword);

//         $entityManager->persist($user);
//         $entityManager->flush();

//         // Simuler la requête de connexion
//         $client->request('POST', '/api/login', [
//             'json' => [
//                 'username' => 'adresse@email.com',
//                 'password' => $password,
//             ]
//         ]);

//         // Vérifier que la réponse a un code HTTP 200
//         $this->assertResponseIsSuccessful();

//         // Récupérer le contenu de la réponse
//         $responseData = json_decode($client->getResponse()->getContent(), true);

//         // Vérifier que les données de la réponse contiennent le bon user, token et rôles
//         $this->assertArrayHasKey('user', $responseData);
//         $this->assertArrayHasKey('apiToken', $responseData);
//         $this->assertArrayHasKey('roles', $responseData);

//         // Vérifier que l'email de l'utilisateur est bien retourné
//         $this->assertEquals('adressetest@email.com', $responseData['user']);

//         // Nettoyer la base de données après le test
//         $entityManager->remove($user);
//         $entityManager->flush();
//     }
// }
