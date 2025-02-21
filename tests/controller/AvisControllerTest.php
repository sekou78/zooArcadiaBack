<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AvisControllerTest extends WebTestCase
{
    public function testApiDocUrlIsSusseccfull(): void
    {
        $client = self::createClient();
        $client->request('Get', '/api/doc');

        self::assertResponseIsSuccessful();
    }

    public function testApiNewAvisIsSusseccfull(): void
    {
        $client = self::createClient();
        $client->followRedirects(false);

        $client->request(
            'POST',
            '/api/avis',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'pseudo' => 'kolo',
                'comments' => 'Super endroit !',
                "isVisible" => false,
            ], JSON_THROW_ON_ERROR)
        );

        // $statusCode = $client->getResponse()->getStatusCode();
        // dd($statusCode);
    }


    public function testCreateAvisSuccessfully()
    {
        // Créez un client pour tester l'API
        $client = self::createClient();

        // Préparez les données à envoyer dans la requête POST
        $data = [
            'pseudo' => 'kolo',
            'comments' => 'Super endroit !'
        ];

        // Faites une requête POST pour créer un avis
        $client->request(
            'POST',
            '/api/avis', // L'URL de la route
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data) // Données de l'avis en format JSON
        );

        // Vérifiez que la réponse est correcte (code 201)
        $this->assertEquals(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());

        // Vérifiez que la réponse contient l'ID, pseudo, commentaire, etc.
        $responseContent = json_decode($client->getResponse()->getContent(), true);
        $this->assertNotNull($responseContent);
        $this->assertArrayHasKey('id', $responseContent);
        $this->assertArrayHasKey('pseudo', $responseContent);
        $this->assertArrayHasKey('comments', $responseContent);
        $this->assertEquals('kolo', $responseContent['pseudo']);
        $this->assertEquals('Super endroit !', $responseContent['comments']);

        // Vérifiez la présence de l'header Location
        $this->assertArrayHasKey('location', $client->getResponse()->headers->all());
        $locationHeader = $client->getResponse()->headers->get('location');
        $this->assertStringContainsString('/api/avis/', $locationHeader); // L'URL doit contenir '/api/avis/'
    }

    public function testCreateAvisMissingData()
    {
        // Créez un client pour tester l'API
        $client = static::createClient();

        // Données invalides (pseudo manquant)
        $data = [
            'comments' => 'Super endroit !'
        ];

        // Faites une requête POST pour créer un avis sans données complètes
        $client->request(
            'POST',
            '/api/avis',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data) // Données manquantes
        );

        // Vérifiez que la réponse est une erreur 400 (Bad Request)
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());

        // Vérifiez que le message d'erreur est bien présent
        $responseContent = json_decode($client->getResponse()->getContent(), true);
        $this->assertNotNull($responseContent);
        $this->assertArrayHasKey('error', $responseContent);
        $this->assertEquals('Pseudo et commentaire sont obligatoires.', $responseContent['error']);
    }
}
