<?php

namespace App\Tests\Entity;

use App\Entity\ServiceRestaurant;
use App\Entity\Service;
use PHPUnit\Framework\TestCase;

class ServiceRestaurantTest extends TestCase
{
    public function testServiceRestaurantCreation()
    {
        // Créer une instance de ServiceRestaurant et une instance de Service
        $serviceRestaurant = new ServiceRestaurant();
        $service = new Service();

        // Définir les propriétés
        $serviceRestaurant->setNom('Restaurant A')
            ->setDescription('Description du restaurant A')
            ->setHeureDebut(new \DateTime('08:00:00'))
            ->setHeureFin(new \DateTime('22:00:00'))
            ->setCreatedAt(new \DateTimeImmutable())
            ->setUpdatedAt(new \DateTimeImmutable())
            ->setService($service);

        // Vérifier que les propriétés sont correctement définies
        $this->assertEquals(
            'Restaurant A',
            $serviceRestaurant->getNom()
        );
        $this->assertEquals(
            'Description du restaurant A',
            $serviceRestaurant->getDescription()
        );
        $this->assertInstanceOf(
            \DateTimeInterface::class,
            $serviceRestaurant->getHeureDebut()
        );
        $this->assertInstanceOf(
            \DateTimeInterface::class,
            $serviceRestaurant->getHeureFin()
        );
        $this->assertInstanceOf(
            \DateTimeImmutable::class,
            $serviceRestaurant->getCreatedAt()
        );
        $this->assertInstanceOf(
            \DateTimeImmutable::class,
            $serviceRestaurant->getUpdatedAt()
        );

        // Vérifier la relation avec le service
        $this->assertSame(
            $service,
            $serviceRestaurant->getService()
        );
    }

    public function testServiceRestaurantWithNullDates()
    {
        // Créer une instance de ServiceRestaurant sans dates initiales
        $serviceRestaurant = new ServiceRestaurant();

        // Vérifier que les dates sont nulles
        $this->assertNull(
            $serviceRestaurant->getCreatedAt()
        );
        $this->assertNull(
            $serviceRestaurant->getUpdatedAt()
        );
        $this->assertNull(
            $serviceRestaurant->getHeureDebut()
        );
        $this->assertNull(
            $serviceRestaurant->getHeureFin()
        );
    }

    public function testSetAndGetService()
    {
        // Créer une instance de ServiceRestaurant et Service
        $serviceRestaurant = new ServiceRestaurant();
        $service = new Service();  // Assurez-vous d'avoir l'entité Service correctement définie

        // Définir le service pour le service restaurant
        $serviceRestaurant->setService($service);

        // Vérifier que le service est correctement assigné
        $this->assertSame(
            $service,
            $serviceRestaurant->getService()
        );
    }
}
