<?php

namespace App\Tests\Controller;

use App\Entity\Image;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageControllerTest extends WebTestCase
{
    private EntityManagerInterface $manager;
    private $client;

    // Initialisation avant chaque test
    protected function setUp(): void
    {
        // Créer le client de test
        $this->client = self::createClient();

        // Récupérer l'EntityManager via le container du client
        $this->manager = $this->client->getContainer()->get(EntityManagerInterface::class);
    }

    private function isFileUploaded(string $directory): bool
    {
        $files = glob($directory . '*');
        return !empty($files);
    }

    // public function testRouteCanUploadImageByAdminIsSuccessful(): void
    // {
    //     $client->followRedirects(false);

    //     //Authentification pour récupérer le token
    //     $client->request(
    //         "POST",
    //         "/api/login",
    //         [],
    //         [],
    //         ["CONTENT_TYPE" => "application/json"],
    //         json_encode([
    //             "username" => "testAdmin@mail.com",
    //             "password" => "Azert$12",
    //         ])
    //     );

    //     //Récupérer le token depuis la réponse
    //     $responseData = json_decode($client->getResponse()->getContent(), true);
    //     $apiToken = $responseData['apiToken'];

    //     // Vérifier que l'image originale existe avant de l'envoyer
    //     $originalImagePath = __DIR__ . '/../../tests/controller/me.jpg';

    //     $this->assertFileExists(
    //         $originalImagePath,
    //         "L'image de test est introuvable après la copie."
    //     );

    //     //Créer un objet UploadedFile avec le bon type MIME
    //     $imageFile = new UploadedFile(
    //         $originalImagePath,         // Fichier à envoyer
    //         'me.jpg',       // Nom du fichier
    //         'image/jpeg',           // Type MIME forcé
    //         null,
    //         true                    // Mode test pour éviter la vérification de l'upload
    //     );

    //     //Envoyer la requête d'upload d'image
    //     $client->request(
    //         'POST',
    //         '/api/image',
    //         [],
    //         ['image' => $imageFile],
    //         [
    //             'CONTENT_TYPE' => 'multipart/form-data',
    //             'HTTP_X_AUTH_TOKEN' => $apiToken,
    //         ]
    //     );

    //     //Vérifier que la réponse est un succès
    //     $statusCode = $client->getResponse()->getStatusCode();
    //     $this->assertEquals(201, $statusCode, "L'upload d'image a échoué.");

    //     //Vérifier que l’image est bien sauvegardée dans le dossier upload
    //     $uploadedFilePath = __DIR__ . '/../../public/uploads/images/';
    //     $this->assertTrue(
    //         $this->isFileUploaded(
    //             $uploadedFilePath
    //         ),
    //         "L'image n'a pas été stockée après l'upload."
    //     );
    // }

    // public function testRouteCanShowImageByAdminIsSuccessful(): void
    // {
    //     $this->client->followRedirects(false);

    //     // Récupérer une image existante
    //     $image = $this->manager
    //         ->getRepository(Image::class)
    //         ->find(12); // ID de l'image existante

    //     // Vérifier que l'image existe en base de données
    //     $this->assertNotNull(
    //         $image,
    //         "L'image avec l'ID 6 n'existe pas en base de données."
    //     );

    //     // Si l'image existe, on peut aussi vérifier que le fichier physique existe
    //     $imagePath = __DIR__ . '/../../public' . $image->getFilePath();
    //     $this->assertFileExists(
    //         $imagePath,
    //         "L'image physique associée à l'ID n'existe pas sur le disque."
    //     );
    // }

    // public function testRouteCanUploadEditImageByAdminIsSuccessful(): void
    // {
    //     $this->client->followRedirects(false);

    //     // Authentification pour récupérer le token
    //     $this->client->request(
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

    //     // Récupérer le token depuis la réponse
    //     $responseData = json_decode($this->client->getResponse()->getContent(), true);
    //     $apiToken = $responseData['apiToken'];

    //     // Récupérer une image existante
    //     $image = $this->manager
    //         ->getRepository(Image::class)
    //         ->find(6); // ID de l'image existante

    //     // Vérifier que l'image existe
    //     $this->assertNotNull(
    //         $image,
    //         "L'image avec l'ID n'existe pas."
    //     );

    //     // Créer une nouvelle image à uploader pour l'édition
    //     $updatedImagePath = __DIR__ . '/../../tests/controller/code.jpg';
    //     $this->assertFileExists(
    //         $updatedImagePath,
    //         "L'image de mise à jour est introuvable."
    //     );

    //     $updatedImageFile = new UploadedFile(
    //         $updatedImagePath,          // Fichier mis à jour à envoyer
    //         'code.jpg',                 // Nom du fichier mis à jour
    //         'image/jpeg',               // Type MIME forcé
    //         null,                       // Aucun paramètre de taille
    //         true                        // Mode test pour éviter la vérification de l'upload
    //     );

    //     // Tester la route PUT pour éditer l'image
    //     $this->client->request(
    //         'POST',
    //         '/api/image/' . $image->getId(), // L'URL pour éditer l'image
    //         [],
    //         ['image' => $updatedImageFile],
    //         [
    //             'CONTENT_TYPE' => 'multipart/form-data',
    //             'HTTP_X_AUTH_TOKEN' => $apiToken,
    //         ]
    //     );

    //     // Vérifier que la réponse est un succès pour la mise à jour
    //     $statusCode = $this->client->getResponse()->getStatusCode();
    //     $this->assertEquals(
    //         200,
    //         $statusCode,
    //         "La mise à jour de l'image a échoué."
    //     );

    //     // Vérifier que l'image existe en base de données après modification
    //     $updatedImage = $this->manager
    //         ->getRepository(Image::class)
    //         ->find($image->getId());
    //     $this->assertNotNull(
    //         $updatedImage,
    //         "L'image mise à jour n'a pas été trouvée en base de données."
    //     );
    //     $this->assertStringContainsString(
    //         'code.jpg',
    //         $updatedImage->getFilePath(),
    //         "Le fichier d'image n'a pas été mis à jour."
    //     );

    //     // Vérifier que le fichier physique a bien été modifié
    //     $imagePath = __DIR__ . '/../../public' . $updatedImage->getFilePath();
    //     $this->assertFileExists(
    //         $imagePath,
    //         "L'image mise à jour n'existe pas sur le disque."
    //     );
    // }

    // public function testRouteCanConnectDeleteImageByAdminIsSuccessful()
    // {
    //     $this->client->followRedirects(false);

    //     // Authentification pour récupérer le token
    //     $this->client->request(
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

    //     // Récupérer le token depuis la réponse
    //     $responseData = json_decode($this->client->getResponse()->getContent(), true);
    //     $apiToken = $responseData['apiToken'];

    //     // ID de l'image à supprimer
    //     $imageId = 13; // Exemple d'ID de l'image à supprimer

    //     // Tester la route DELETE pour supprimer l'image
    //     $this->client->request(
    //         'DELETE',
    //         '/api/image/' . $imageId, // L'URL pour supprimer l'image
    //         [],
    //         [],
    //         [
    //             'CONTENT_TYPE' => 'application/json',
    //             'HTTP_X_AUTH_TOKEN' => $apiToken,
    //         ]
    //     );

    //     // Vérifier que la réponse est un succès pour la suppression
    //     $statusCode = $this->client->getResponse()->getStatusCode();
    //     $this->assertEquals(
    //         200,
    //         $statusCode,
    //         "La suppression de l'image a échoué."
    //     );

    //     // Vérifier que l'image n'existe plus en base de données
    //     $deletedImage = $this->manager
    //         ->getRepository(Image::class)
    //         ->find($imageId);

    //     // L'image ne doit pas exister après la suppression
    //     $this->assertNull(
    //         $deletedImage,
    //         "L'image avec l'ID " . $imageId . " n'a pas été supprimée en base de données."
    //     );
    // }

    // public function testRouteCanConnectListImageByAdminIsSuccessful()
    // {
    //     $this->client->followRedirects(false);

    //     // 1. Authentification pour récupérer le token
    //     $this->client->request(
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
    //     $responseData = json_decode($this->client->getResponse()->getContent(), true);
    //     $apiToken = $responseData['apiToken'];

    //     // 3. Créer un utilisateur en tant qu'admin, avec le token dans l'en-tête
    //     $this->client->request(
    //         'GET',
    //         "/api/image/api/rapports",
    //         [],
    //         [],
    //         [
    //             'CONTENT_TYPE' => 'application/json',
    //             'HTTP_X_AUTH_TOKEN' => $apiToken,
    //         ]
    //     );

    //     // 4. Vérifier la réponse
    //     $statusCode = $this->client->getResponse()->getStatusCode();
    //     $this->assertEquals(200, $statusCode);
    // }
}
