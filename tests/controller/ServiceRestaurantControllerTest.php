<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ServiceRestaurantControllerTest extends WebTestCase
{
    // public function testRouteCanConnectCreateServiceRestaurantByAdminAndEmployeIsSuccessful(): void
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
    //         '/api/serviceRestaurant',
    //         [],
    //         [],
    //         [
    //             'CONTENT_TYPE' => 'application/json',
    //             'HTTP_X_AUTH_TOKEN' => $apiToken,
    //         ],
    //         json_encode([
    //             "nom" => "Sercive restaurant 1",
    //             "description" => "Produits et boissons de la region",
    //             "heureService" => "10:00 - 15:00",
    //             "service" => 1
    //         ], JSON_THROW_ON_ERROR)
    //     );

    //     //Vérifier la réponse
    //     $statusCode = $client->getResponse()->getStatusCode();
    //     $this->assertEquals(201, $statusCode);
    // }

    // public function testRouteCanConnectShowServiceRestaurantByAdminIsInvalid(): void
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
    //         '/api/serviceRestaurant/9999',
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
    //         '/api/serviceRestaurant/2',
    //         [],
    //         [],
    //         [
    //             'CONTENT_TYPE' => 'application/json',
    //             'HTTP_X_AUTH_TOKEN' => $apiToken,
    //         ],
    //         json_encode([
    //             "nom" => "Sercive restaurant 1 modifie",
    //             "description" => "Produits et boissons de la region modifie",
    //             "heureService" => "13:00 - 23:00",
    //             "service" => 2
    //         ], JSON_THROW_ON_ERROR)
    //     );

    //     //Vérifier la réponse
    //     $statusCode = $client->getResponse()->getStatusCode();
    //     $this->assertEquals(200, $statusCode);
    // }

    // public function testRouteCanConnectDeleteServiceRestaurantByAdminIsNotFound()
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
    //         '/api/serviceRestaurant/9999',
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
}
