<?php

// namespace App\Tests\Controller;

// use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

// class ServiceControllerTest extends WebTestCase
// {
    // public function testRouteCanConnectCreateServiceByAdminIsSuccessful(): void
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
    //             "username" => "testAdmin@mail.com",
    //             "password" => "Azert$12",
    //         ])
    //     );

    //     //Récupérer le token depuis la réponse
    //     $responseData = json_decode($client->getResponse()->getContent(), true);
    //     $apiToken = $responseData['apiToken'];

    //     //Créer un Habitat en tant qu'admin, avec le token dans l'en-tête
    //     $client->request(
    //         'POST',
    //         '/api/service',
    //         [],
    //         [],
    //         [
    //             'CONTENT_TYPE' => 'application/json',
    //             'HTTP_X_AUTH_TOKEN' => $apiToken,
    //         ],
    //         json_encode([
    //             "nom" => "Nettoyage des cages",
    //             "description" => "Nettoyage quotidien des cages des animaux"
    //         ], JSON_THROW_ON_ERROR)
    //     );

    //     //Vérifier la réponse
    //     $statusCode = $client->getResponse()->getStatusCode();
    //     $this->assertEquals(201, $statusCode);
    // }

    // public function testRouteCanConnectShowServiceByAdminIsInvalid(): void
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
    //             "username" => "testAdmin@mail.com",
    //             "password" => "Azert$12",
    //         ])
    //     );

    //     //Récupérer le token depuis la réponse
    //     $responseData = json_decode($client->getResponse()->getContent(), true);
    //     $apiToken = $responseData['apiToken'];

    //     $client->request(
    //         'GET',
    //         '/api/service/9999',
    //         [],
    //         [],
    //         [
    //             'CONTENT_TYPE' => 'application/json',
    //             'HTTP_X_AUTH_TOKEN' => $apiToken,
    //         ]
    //     );

    //     //Vérifier la réponse
    //     $statusCode = $client->getResponse()->getStatusCode();
    //     $this->assertEquals(404, $statusCode);
    // }

    // public function testRouteCanConnectEditServiceByAdminIsSuccessful(): void
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
    //             "username" => "testAdmin@mail.com",
    //             "password" => "Azert$12",
    //         ], JSON_THROW_ON_ERROR)
    //     );

    //     //Récupérer le token depuis la réponse
    //     $responseData = json_decode($client->getResponse()->getContent(), true);
    //     $apiToken = $responseData['apiToken'];

    //     //Mise à jour de l'animal
    //     $client->request(
    //         'PUT',
    //         '/api/service/2',
    //         [],
    //         [],
    //         [
    //             'CONTENT_TYPE' => 'application/json',
    //             'HTTP_X_AUTH_TOKEN' => $apiToken,
    //         ],
    //         json_encode([
    //             "nom" => "Nettoyage des enclos",
    //             "description" => "Nettoyage quotidien des enclos des animaux"
    //         ], JSON_THROW_ON_ERROR)
    //     );

    //     //Vérifier la réponse
    //     $statusCode = $client->getResponse()->getStatusCode();
    //     $this->assertEquals(200, $statusCode);
    // }

    // public function testRouteCanConnectDeleteServiceByAdminIsNotFound()
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
    //             "username" => "testAdmin@mail.com",
    //             "password" => "Azert$12",
    //         ])
    //     );

    //     //Récupérer le token depuis la réponse
    //     $responseData = json_decode($client->getResponse()->getContent(), true);
    //     $apiToken = $responseData['apiToken'];

    //     //Mise à jour de l'animal
    //     $client->request(
    //         'DELETE',
    //         '/api/service/9999',
    //         [],
    //         [],
    //         [
    //             'CONTENT_TYPE' => 'application/json',
    //             'HTTP_X_AUTH_TOKEN' => $apiToken,
    //         ]
    //     );

    //     //Vérifier la réponse
    //     $this->assertResponseStatusCodeSame(404);
    // }

//     public function testRouteCanConnectListServiceByAdminIsSuccessful()
//     {
//         $client = self::createClient();
//         $client->followRedirects(false);

//         // 1. Authentification pour récupérer le token
//         $client->request(
//             "POST",
//             "/api/login",
//             [],
//             [],
//             [
//                 "CONTENT_TYPE" => "application/json",
//             ],
//             json_encode([
//                 "username" => "testAdmin@mail.com",
//                 "password" => "Azert$12",
//             ])
//         );

//         // 2. Récupérer le token depuis la réponse
//         $responseData = json_decode($client->getResponse()->getContent(), true);
//         $apiToken = $responseData['apiToken'];

//         // 3. Créer un utilisateur en tant qu'admin, avec le token dans l'en-tête
//         $client->request(
//             'GET',
//             "/api/service/api/services",
//             [],
//             [],
//             [
//                 'CONTENT_TYPE' => 'application/json',
//                 'HTTP_X_AUTH_TOKEN' => $apiToken,
//             ]
//         );

//         // 4. Vérifier la réponse
//         $statusCode = $client->getResponse()->getStatusCode();
//         $this->assertEquals(200, $statusCode);
//     }
// }
