<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HabitatControllerTest extends WebTestCase
{
    // public function testRouteCanConnectCreateHabitatByAdminIsSuccessful(): void
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
    //         '/api/habitat',
    //         [],
    //         [],
    //         [
    //             'CONTENT_TYPE' => 'application/json',
    //             'HTTP_X_AUTH_TOKEN' => $apiToken,
    //         ],
    //         json_encode([
    //             "name" => "testHabitat",
    //             "description" => "testDescription",
    //             "commentHabitat" => "testCommentHabitat",
    //         ], JSON_THROW_ON_ERROR)
    //     );

    //     //Vérifier la réponse
    //     $statusCode = $client->getResponse()->getStatusCode();
    //     $this->assertEquals(201, $statusCode);
    // }

    // public function testRouteCanConnectShowHabitatByAdminIsInvalid(): void
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
    //         '/api/habitat/9999',
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

    // public function testRouteCanConnectEditHabitatByAdminIsSuccessful(): void
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
    //         '/api/habitat/3',
    //         [],
    //         [],
    //         [
    //             'CONTENT_TYPE' => 'application/json',
    //             'HTTP_X_AUTH_TOKEN' => $apiToken,
    //         ],
    //         json_encode([
    //             "name" => "testHabitat modifié",
    //             "description" => "testDescription modifiée",
    //             "commentHabitat" => "testCommentHabitat modifié"
    //         ], JSON_THROW_ON_ERROR)
    //     );

    //     //Vérifier la réponse
    //     $statusCode = $client->getResponse()->getStatusCode();
    //     $this->assertEquals(200, $statusCode);
    // }

    // public function testRouteCanConnectDeleteHabitatByAdminIsNotFound()
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
    //         '/api/habitat/9999',
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

    // public function testRouteCanConnectListHabitatByAdminIsSuccessful()
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
    //         "/api/habitat/api/rapports",
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
