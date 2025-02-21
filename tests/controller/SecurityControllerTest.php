<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecurityControllerTest extends WebTestCase
{
    // public function testApiDocUrlIsSuccessful(): void
    // {
    //     $client = self::createClient();
    //     $client->followRedirects(false);
    //     $client->request("GET", "/api/doc");

    //     self::assertResponseIsSuccessful();
    // }

    // public function testRegistrationAdminRouteCanConnectValid(): void
    // {
    //     $client = self::createClient();
    //     $client->followRedirects(false);

    //     $client->request(
    //         'POST',
    //         '/api/registration',
    //         [],
    //         [],
    //         [
    //             'CONTENT_TYPE' => 'application/json',
    //         ],
    //         json_encode([
    //             "email" => "testAdmin@mail.com",
    //             "password" => "Azert$12",
    //             'roles' => ['ROLE_ADMIN'],
    //             "username" => "test",
    //             "nom" => "test",
    //             "prenom" => "test"
    //         ], JSON_THROW_ON_ERROR)
    //     );

    //     $statusCode = $client->getResponse()->getStatusCode();
    //     $this->assertEquals(201, $statusCode);
    // }

    // public function testRegistrationAdminRouteCanConnectInvalid(): void
    // {
    //     $client = self::createClient();
    //     $client->followRedirects(false);

    //     $client->request(
    //         'POST',
    //         '/api/registration',
    //         [],
    //         [],
    //         [
    //             'CONTENT_TYPE' => 'application/json',
    //         ],
    //         json_encode([
    //             "email" => "testAdmin@mail.com",
    //             "password" => "Azert$12",
    //             'roles' => ['ROLE_ADMIN'],
    //             "username" => "test",
    //             "nom" => "test",
    //             "prenom" => "test"
    //         ], JSON_THROW_ON_ERROR)
    //     );

    //     $statusCode = $client->getResponse()->getStatusCode();
    //     $this->assertEquals(403, $statusCode);
    // }

    // public function testRouteCanConnectCreateUserForAdminValid(): void
    // {
    //     $client = self::createClient();
    //     $client->followRedirects(false);

    //     // 1. Authentification pour récupérer le token
    //     $client->request(
    //         "POST",
    //         "/api/login",
    //         [],
    //         [],
    //         [
    //             "CONTENT_TYPE" => "application/json",
    //         ],
    //         json_encode([
    //             "username" => "testAdmin@mail.com",
    //             "password" => "Azert$12",
    //         ])
    //     );

    //     // 2. Récupérer le token depuis la réponse
    //     $responseData = json_decode($client->getResponse()->getContent(), true);
    //     $apiToken = $responseData['apiToken'];

    //     // 3. Créer un utilisateur en tant qu'admin, avec le token dans l'en-tête
    //     $client->request(
    //         'POST',
    //         '/api/admin/create-user',
    //         [],
    //         [],
    //         [
    //             'CONTENT_TYPE' => 'application/json',
    //             'HTTP_X_AUTH_TOKEN' => $apiToken,
    //         ],
    //         json_encode([
    //             "email" => "testemploye@mail.com",
    //             // "email" => "testveterinaire@mail.com",
    //             "password" => "Azert$12",
    //             'roles' => ['ROLE_EMPLOYE'],
    //             // 'roles' => ['ROLE_VETERINAIRE'],
    //             "username" => "testemploye",
    //             // "username" => "testveterinaire",
    //             "nom" => "testemploye",
    //             // "nom" => "testveterinaire",
    //             "prenom" => "testemploye",
    //             // "prenom" => "testveterinaire"
    //         ], JSON_THROW_ON_ERROR)
    //     );

    //     // 4. Vérifier la réponse
    //     $statusCode = $client->getResponse()->getStatusCode();
    //     $this->assertEquals(201, $statusCode);
    // }

    // public function testRouteCanConnectCreateUserForAdminInvalid(): void
    // {
    //     $client = self::createClient();
    //     $client->followRedirects(false);

    //     // 1. Authentification pour récupérer le token
    //     $client->request(
    //         "POST",
    //         "/api/login",
    //         [],
    //         [],
    //         [
    //             "CONTENT_TYPE" => "application/json",
    //         ],
    //         json_encode([
    //             "username" => "testAdmin@mail.com",
    //             "password" => "Azert$12",
    //         ])
    //     );

    //     // 2. Récupérer le token depuis la réponse
    //     $responseData = json_decode($client->getResponse()->getContent(), true);
    //     $apiToken = $responseData['apiToken'];

    //     // 3. Créer un utilisateur en tant qu'admin, avec le token dans l'en-tête
    //     $client->request(
    //         'POST',
    //         '/api/admin/create-user',
    //         [],
    //         [],
    //         [
    //             'CONTENT_TYPE' => 'application/json',
    //             'HTTP_X_AUTH_TOKEN' => $apiToken,
    //         ],
    //         json_encode([
    //             // "email" => "testemploye@mail.com",
    //             "email" => "testveterinaire@mail.com",
    //             "password" => "Azert$12",
    //             // 'roles' => ['ROLE_EMPLOYE'],
    //             'roles' => ['ROLE_VETERINAIRE'],
    //             "username" => "testveterinaire",
    //             "nom" => "testveterinaire",
    //             "prenom" => "testveterinaire"
    //         ], JSON_THROW_ON_ERROR)
    //     );

    //     // 4. Vérifier la réponse
    //     $statusCode = $client->getResponse()->getStatusCode();
    //     $this->assertEquals(400, $statusCode);
    // }

    // public function testRouteCanConnectLoginValid(): void
    // {
    //     $client = self::createClient();
    //     $client->followRedirects(false);

    //     $client->request(
    //         "POST",
    //         "/api/login",
    //         [],
    //         [],
    //         [
    //             "CONTENT_TYPE" => "application/json",
    //         ],
    //         json_encode([
    //             "username" => "testAdmin@mail.com",
    //             "password" => "Azert$12",
    //         ])
    //     );

    //     $statusCode = $client->getResponse()->getStatusCode();
    //     $this->assertEquals(200, $statusCode);
    // }

    // public function testRouteCanConnectLoginInvalid(): void
    // {
    //     $client = self::createClient();
    //     $client->followRedirects(false);

    //     $client->request(
    //         "POST",
    //         "/api/login",
    //         [],
    //         [],
    //         [
    //             "CONTENT_TYPE" => "application/json",
    //         ],
    //         json_encode([
    //             "username" => "testAdmin@mail.com",
    //             "password" => "Azert$136",
    //         ])
    //     );

    //     $statusCode = $client->getResponse()->getStatusCode();
    //     $this->assertEquals(401, $statusCode);
    // }

    // public function testApiAccountMeUrlIsSecure(): void
    // {
    //     $client = self::createClient();
    //     $client->followRedirects(false);

    //     $client->request("GET", "/api/account/me");

    //     self::assertResponseStatusCodeSame(401);
    // }

    // public function testApiAccountEditUrlIsSecure(): void
    // {
    //     $client = self::createClient();
    //     $client->followRedirects(false);

    //     $client->request("PUT", "/api/account/edit");

    //     self::assertResponseStatusCodeSame(401);
    // }

    // public function testRouteCanConnectResetPasswordForAdminIvalid(): void
    // {
    //     $client = self::createClient();
    //     $client->followRedirects(false);

    //     // 1. Authentification pour récupérer le token
    //     $client->request(
    //         "POST",
    //         "/api/login",
    //         [],
    //         [],
    //         [
    //             "CONTENT_TYPE" => "application/json",
    //         ],
    //         json_encode([
    //             "username" => "testAdmin@mail.com",
    //             "password" => "Azert$12",
    //         ])
    //     );

    //     // 2. Récupérer le token depuis la réponse
    //     $responseData = json_decode($client->getResponse()->getContent(), true);
    //     $apiToken = $responseData['apiToken'];

    //     // 3. Créer un utilisateur en tant qu'admin, avec le token dans l'en-tête
    //     $client->request(
    //         'POST',
    //         "/api/admin/reset-password/{username}",
    //         [],
    //         [],
    //         [
    //             'CONTENT_TYPE' => 'application/json',
    //             'HTTP_X_AUTH_TOKEN' => $apiToken,
    //         ],
    //         json_encode([
    //             "username" => "testemploye",
    //         ], JSON_THROW_ON_ERROR)
    //     );

    //     // 4. Vérifier la réponse
    //     $statusCode = $client->getResponse()->getStatusCode();
    //     $this->assertEquals(404, $statusCode);
    // }

    // public function testRouteCanConnectListForAdminValid(): void
    // {
    //     $client = self::createClient();
    //     $client->followRedirects(false);

    //     // 1. Authentification pour récupérer le token
    //     $client->request(
    //         "POST",
    //         "/api/login",
    //         [],
    //         [],
    //         [
    //             "CONTENT_TYPE" => "application/json",
    //         ],
    //         json_encode([
    //             "username" => "testAdmin@mail.com",
    //             "password" => "Azert$12",
    //         ])
    //     );

    //     // 2. Récupérer le token depuis la réponse
    //     $responseData = json_decode($client->getResponse()->getContent(), true);
    //     $apiToken = $responseData['apiToken'];

    //     // 3. Créer un utilisateur en tant qu'admin, avec le token dans l'en-tête
    //     $client->request(
    //         'GET',
    //         "/api/admin/dashboardAnimal",
    //         [],
    //         [],
    //         [
    //             'CONTENT_TYPE' => 'application/json',
    //             'HTTP_X_AUTH_TOKEN' => $apiToken,
    //         ]
    //     );

    //     // 4. Vérifier la réponse
    //     $statusCode = $client->getResponse()->getStatusCode();
    //     $this->assertEquals(200, $statusCode);
    // }
}
