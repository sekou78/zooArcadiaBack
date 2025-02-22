<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RapportVeterinaireControllerTest extends WebTestCase
{
    // public function testRouteCanConnectCreateRapportVeterinaireByVeterinaireIsSuccessful(): void
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
    //             "username" => "testveterinaire@mail.com",
    //             "password" => "Azert$12",
    //         ])
    //     );

    //     //Récupérer le token depuis la réponse
    //     $responseData = json_decode($client->getResponse()->getContent(), true);
    //     $apiToken = $responseData['apiToken'];

    //     //Créer un Rapport Veterinaire en tant qu'admin, avec le token dans l'en-tête
    //     $client->request(
    //         'POST',
    //         '/api/rapportVeterinaire',
    //         [],
    //         [],
    //         [
    //             'CONTENT_TYPE' => 'application/json',
    //             'HTTP_X_AUTH_TOKEN' => $apiToken,
    //         ],
    //         json_encode([
    //             "animal" => 1,
    //             "date" => "2022-01-01",
    //             "etat" => "Bon",
    //             "nourritureProposee" => "Croquettes",
    //             "quantiteNourriture" => 10,
    //             "commentaireHabitat" => "Habitat parfait",
    //         ], JSON_THROW_ON_ERROR)
    //     );

    //     //Vérifier la réponse
    //     $statusCode = $client->getResponse()->getStatusCode();
    //     $this->assertEquals(201, $statusCode);
    // }

    // public function testRouteCanConnectShowRapportVeterinaireIsInvalid(): void
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
    //             "username" => "testveterinaire@mail.com",
    //             "password" => "Azert$12",
    //         ])
    //     );

    //     //Récupérer le token depuis la réponse
    //     $responseData = json_decode($client->getResponse()->getContent(), true);
    //     $apiToken = $responseData['apiToken'];

    //     $client->request(
    //         'GET',
    //         '/api/rapportVeterinaire/9999',
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

    // public function testRouteCanConnectEditRapportVeterinairebByVeterinaireIsSuccessful(): void
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
    //             "username" => "testveterinaire@mail.com",
    //             "password" => "Azert$12",
    //         ])
    //     );

    //     //Récupérer le token depuis la réponse
    //     $responseData = json_decode($client->getResponse()->getContent(), true);
    //     $apiToken = $responseData['apiToken'];

    //     //Mise à jour de l'animal
    //     $client->request(
    //         'PUT',
    //         '/api/rapportVeterinaire/2',
    //         [],
    //         [],
    //         [
    //             'CONTENT_TYPE' => 'application/json',
    //             'HTTP_X_AUTH_TOKEN' => $apiToken,
    //         ],
    //         json_encode([
    //             "animal" => 2,
    //             "date" => "10-10-2025",
    //             "etat" => "Mauvais",
    //             "nourritureProposee" => "Croquettes",
    //             "quantiteNourriture" => 200,
    //             "commentaireHabitat" => "Habitat defaillant",
    //         ], JSON_THROW_ON_ERROR)
    //     );

    //     //Vérifier la réponse
    //     $statusCode = $client->getResponse()->getStatusCode();
    //     $this->assertEquals(200, $statusCode);
    // }

    // public function testRouteCanConnectDeleteRapportVeterinaireByVeterinaireIsNotFound()
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
    //             "username" => "testveterinaire@mail.com",
    //             "password" => "Azert$12",
    //         ])
    //     );

    //     //Récupérer le token depuis la réponse
    //     $responseData = json_decode($client->getResponse()->getContent(), true);
    //     $apiToken = $responseData['apiToken'];

    //     //Mise à jour de l'animal
    //     $client->request(
    //         'DELETE',
    //         '/api/rapportVeterinaire/9999',
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

    public function testRouteCanConnectListHabitatByAdminIsSuccessful()
    {
        $client = self::createClient();
        $client->followRedirects(false);

        // 1. Authentification pour récupérer le token
        $client->request(
            "POST",
            "/api/login",
            [],
            [],
            [
                "CONTENT_TYPE" => "application/json",
            ],
            json_encode([
                "username" => "testveterinaire@mail.com",
                "password" => "Azert$12",
            ])
        );

        // 2. Récupérer le token depuis la réponse
        $responseData = json_decode($client->getResponse()->getContent(), true);
        $apiToken = $responseData['apiToken'];

        // 3. Créer un utilisateur en tant qu'admin, avec le token dans l'en-tête
        $client->request(
            'GET',
            "/api/rapportVeterinaire/api/rapports",
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X_AUTH_TOKEN' => $apiToken,
            ]
        );

        // 4. Vérifier la réponse
        $statusCode = $client->getResponse()->getStatusCode();
        $this->assertEquals(200, $statusCode);
    }
}
