<?php

// namespace App\Tests\Controller;

// use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
// use Symfony\Component\HttpFoundation\JsonResponse;
// use Symfony\Component\HttpFoundation\Request;
// use PHPUnit\Framework\MockObject\MockObject;
// use Doctrine\ORM\EntityManagerInterface;
// use Symfony\Component\Serializer\SerializerInterface;
// use App\Repository\AnimalRepository;
// use Psr\Log\LoggerInterface;
// use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
// use App\Controller\AnimalController;
// use App\Entity\Animal;

// class AnimalControllerTest extends WebTestCase
// {
//     public function testAnimalCreationEndpointIsAccessible(): void
//     {
//         // Créer un client HTTP pour simuler les requêtes
//         $client = static::createClient();

//         // Effectuer une requête POST à l'endpoint
//         $client->request(
//             'POST',
//             '/api/animal',
//             [],
//             [],
//             [
//                 'CONTENT_TYPE' => 'application/json'
//             ],
//             json_encode([
//                 'firstname' => 'Bamba',
//                 'etat' => 'Sain'
//             ])
//         );

//         // Vérifier que l'API répond (même avec un échec d'authentification)
//         $this->assertResponseStatusCodeSame(
//             401,
//             "L'authentification est requise"
//         );
//     }

//     public function testShowAnimalSuccess(): void
//     {
//         //Créer un client HTTP
//         $client = static::createClient();

//         //Simuler un animal existant
//         $animal = new Animal();
//         $animal->setFirstname('Bamba');
//         $animal->setEtat('Sain');
//         $animal->setCreatedAt(new \DateTimeImmutable());

//         //Créer un Mock du repository
//         $animalRepositoryMock = $this->createMock(AnimalRepository::class);
//         $animalRepositoryMock->method('findOneBy')->willReturn($animal);

//         //Remplacer le vrai repository par le mock
//         self::getContainer()->set(
//             AnimalRepository::class,
//             $animalRepositoryMock
//         );

//         //Faire la requête pour récupérer l'animal
//         $client->request('GET', '/api/animal/1');

//         //Vérifier la réponse
//         $this->assertResponseIsSuccessful();
//         $responseData = json_decode(
//             $client->getResponse()
//                 ->getContent(),
//             true
//         );

//         //Vérifier les données retournées
//         $this->assertArrayHasKey(
//             'firstname',
//             $responseData
//         );
//         $this->assertEquals(
//             'Bamba',
//             $responseData['firstname']
//         );
//     }

//     // public function testEditAnimalSuccessfully()
//     // {
//     //     // Créez un client pour tester l'API
//     //     $client = static::createClient();

//     //     // Créez un animal fictif à mettre à jour
//     //     $existingAnimalId = 1; // Supposons qu'il y ait déjà un animal avec cet ID en base
//     //     $newData = [
//     //         'name' => 'New Name', // Exemple de nouvelles données à mettre à jour
//     //         'species' => 'New Species',
//     //     ];

//     //     // Faites une requête PUT à la route
//     //     $client->request(
//     //         'PUT',
//     //         'api/animal/' . $existingAnimalId, // Assurez-vous que l'URL est correcte
//     //         [],
//     //         [],
//     //         ['CONTENT_TYPE' => 'application/json'],
//     //         json_encode($newData)
//     //     );

//     //     // Vérifiez si la réponse est correcte
//     //     $this->assertEquals(JsonResponse::HTTP_OK, $client->getResponse()->getStatusCode());

//     //     // Vérifiez que le contenu de la réponse est ce à quoi vous vous attendez
//     //     $responseContent = json_decode($client->getResponse()->getContent(), true);
//     //     $this->assertNotNull($responseContent);
//     //     $this->assertEquals('New Name', $responseContent['name']);
//     //     $this->assertEquals('New Species', $responseContent['species']);
//     // }

//     public function testEditAnimalNotFound()
//     {
//         $client = static::createClient();
//         $id = 9999; // Un ID qui n'existe pas

//         // Faites une requête PUT pour essayer de mettre à jour un animal qui n'existe pas
//         $client->request('PUT', '/api/animal' . $id, [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([]));

//         // Assurez-vous que la réponse est NOT FOUND (HTTP 404)
//         $this->assertResponseStatusCodeSame(JsonResponse::HTTP_NOT_FOUND);
//     }

//     // public function testDeleteAnimalSuccessfully()
//     // {
//     //     // Créez un client pour tester l'API
//     //     $client = static::createClient();

//     //     // Simulez une requête DELETE, en contournant la logique de sécurité
//     //     // L'ID de l'animal à supprimer
//     //     $existingAnimalId = 1; // ID d'un animal à supprimer, supposé existant dans votre logique métier

//     //     // Faites une requête DELETE à la route
//     //     $client->request(
//     //         'DELETE',
//     //         'api/animal' . $existingAnimalId // L'URL de la route
//     //     );

//     //     // Vérifiez si la réponse est correcte (suppression réussie)
//     //     $this->assertEquals(JsonResponse::HTTP_OK, $client->getResponse()->getStatusCode());

//     //     // Vérifiez que le message de succès est présent dans la réponse
//     //     $responseContent = json_decode($client->getResponse()->getContent(), true);
//     //     $this->assertNotNull($responseContent);
//     //     $this->assertEquals('Animal deleted successfully', $responseContent['message']);
//     // }


//     public function testDeleteAnimalNotFound()
//     {
//         // Crée un client pour tester l'API
//         $client = static::createClient();

//         // Tenter de supprimer un animal avec un ID inexistant
//         $nonExistentAnimalId = 9999; // ID qui n'existe pas dans la base de données

//         // Effectuer la requête DELETE
//         $client->request('DELETE', 'api/animal' . $nonExistentAnimalId);

//         // Vérifier si la réponse est une erreur 404
//         $this->assertEquals(JsonResponse::HTTP_NOT_FOUND, $client->getResponse()->getStatusCode());
//     }
// }
