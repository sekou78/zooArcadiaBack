<?php

namespace App\Tests\Entity;

use App\Entity\Service;
use PHPUnit\Framework\TestCase;

class ServiceTest extends TestCase
{
    public function testServiceCreationWithoutPrePersist()
    {
        // Créez une instance de l'entité Service
        $service = new Service();

        // Définir les propriétés requises, ici juste les dates
        $service->setNom('Test Service')
            ->setDescription('Description du service de test');

        // Simulez l'initialisation manuelle des dates
        $now = new \DateTimeImmutable();
        $service->setCreatedAt($now);
        $service->setUpdatedAt($now);

        // Vérifiez que les dates sont correctement définies comme des objets DateTimeImmutable
        $this->assertInstanceOf(
            \DateTimeImmutable::class,
            $service->getCreatedAt()
        );
        $this->assertInstanceOf(
            \DateTimeImmutable::class,
            $service->getUpdatedAt()
        );

        // Vérifiez que 'createdAt' et 'updatedAt' sont égales à la date actuelle lors de la création
        $this->assertEquals(
            $service->getCreatedAt(),
            $service->getUpdatedAt()
        );

        // Simulez un changement de la date de mise à jour
        $newUpdatedAt = new \DateTimeImmutable('+1 hour');
        $service->setUpdatedAt($newUpdatedAt);

        // Vérifiez que la date de mise à jour est bien modifiée
        $this->assertNotEquals(
            $service->getCreatedAt(),
            $service->getUpdatedAt()
        );
        $this->assertEquals(
            $newUpdatedAt,
            $service->getUpdatedAt()
        );
    }

    public function testServiceWithNullDates()
    {
        // Créez une instance de Service sans dates initiales
        $service = new Service();

        // Vérifiez que les dates sont nulles au début
        $this->assertNull($service->getCreatedAt());
        $this->assertNull($service->getUpdatedAt());
    }
}
