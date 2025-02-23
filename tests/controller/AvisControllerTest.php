<?php

// namespace App\Tests\Controller;

// use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
// use Symfony\Component\HttpFoundation\Response;

// class AvisControllerTest extends WebTestCase
// {
    // public function testApiNewAvisIsSusseccfull(): void
    // {
    //     $client = self::createClient();
    //     $client->followRedirects(false);

    //     $client->request(
    //         'POST',
    //         '/api/avis',
    //         [],
    //         [],
    //         ['CONTENT_TYPE' => 'application/json'],
    //         json_encode([
    //             'pseudo' => 'kolo',
    //             'comments' => 'Super endroit !',
    //             "isVisible" => false,
    //         ], JSON_THROW_ON_ERROR)
    //     );

    //     $statusCode = $client->getResponse()->getStatusCode();
    //     $this->assertEquals(201, $statusCode);
    // }

    // public function testApiValidEmployeIsSusseccfull(): void
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
    //             "username" => "testemploye@mail.com",
    //             "password" => "Azert$12",
    //         ])
    //     );

    //     // 2. Récupérer le token depuis la réponse
    //     $responseData = json_decode($client->getResponse()->getContent(), true);
    //     $apiToken = $responseData['apiToken'];

    //     // 3. Créer un utilisateur en tant qu'admin, avec le token dans l'en-tête
    //     $client->request(
    //         'PUT',
    //         "/api/avis/employee/validate-avis/2",
    //         [],
    //         [],
    //         [
    //             'CONTENT_TYPE' => 'application/json',
    //             'HTTP_X_AUTH_TOKEN' => $apiToken,
    //         ],
    //         json_encode([
    //             "isVisible" => true,
    //         ], JSON_THROW_ON_ERROR)
    //     );

    //     // 4. Vérifier la réponse
    //     $statusCode = $client->getResponse()->getStatusCode();
    //     $this->assertEquals(200, $statusCode);
    // }

    // public function testRouteCanConnectAvisShowValid(): void
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
    //             "username" => "testemploye@mail.com",
    //             "password" => "Azert$12",
    //         ])
    //     );

    //     $responseData = json_decode($client->getResponse()->getContent(), true);
    //     $apiToken = $responseData['apiToken'];

    //     $client->request(
    //         "GET",
    //         "/api/avis/2",
    //         [],
    //         [],
    //         [
    //             'CONTENT_TYPE' => 'application/json',
    //             'HTTP_X_AUTH_TOKEN' => $apiToken,
    //         ]
    //     );

    //     $statusCode = $client->getResponse()->getStatusCode();
    //     $this->assertEquals(200, $statusCode);
    // }

    // public function testRouteCanConnectAvisIndexValid(): void
    // {
    //     $client = self::createClient();
    //     $client->followRedirects(false);

    //     $client->request(
    //         "GET",
    //         "/api/avis/",
    //         [],
    //         [],
    //         [
    //             'CONTENT_TYPE' => 'application/json',
    //         ]
    //     );

    //     $statusCode = $client->getResponse()->getStatusCode();
    //     $this->assertEquals(200, $statusCode);
    // }

    // public function testDeleteAvisIsSuccess(): void
    // {
    //     $client = self::createClient();
    //     $client->followRedirects(false);

    //     //Authentification pour récupérer le token
    //     $client->request(
    //         "POST",
    //         "/api/login",
    //         [],
    //         [],
    //         [
    //             "CONTENT_TYPE" => "application/json",
    //         ],
    //         json_encode([
    //             "username" => "testemploye@mail.com",
    //             "password" => "Azert$12",
    //         ])
    //     );

    //     //Récupérer le token depuis la réponse
    //     $responseData = json_decode($client->getResponse()->getContent(), true);
    //     $apiToken = $responseData['apiToken'];

    //     //Mise à jour de l'animal
    //     $client->request(
    //         'DELETE',
    //         '/api/avis/5',
    //         [],
    //         [],
    //         [
    //             'CONTENT_TYPE' => 'application/json',
    //             'HTTP_X_AUTH_TOKEN' => $apiToken,
    //         ]
    //     );

    //     //Vérifier la réponse
    //     $this->assertResponseStatusCodeSame(200);
    // }
// }
