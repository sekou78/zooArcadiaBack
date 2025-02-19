<?php

namespace App\Tests\Entity;

use App\Entity\ServiceVisitePetitTrain;
use App\Entity\Service;
use PHPUnit\Framework\TestCase;

class ServiceVisitePetitTrainTest extends TestCase
{
    public function testServiceVisitePetitTrainCreation()
    {
        // Créer une instance de ServiceVisitePetitTrain et Service
        $serviceVisitePetitTrain = new ServiceVisitePetitTrain();
        $service = new Service();

        // Définir les propriétés
        $serviceVisitePetitTrain->setParcours('Parcours A')
            ->setDescription('Description du parcours A')
            ->setDisponibilite(['Lundi', 'Mardi', 'Mercredi'])
            ->setDuree('2 heures')
            ->setCreatedAt(new \DateTimeImmutable())
            ->setUpdatedAt(new \DateTimeImmutable())
            ->setService($service);

        // Vérifier que les propriétés sont correctement définies
        $this->assertEquals(
            'Parcours A',
            $serviceVisitePetitTrain->getParcours()
        );
        $this->assertEquals(
            'Description du parcours A',
            $serviceVisitePetitTrain->getDescription()
        );
        $this->assertEquals(
            ['Lundi', 'Mardi', 'Mercredi'],
            $serviceVisitePetitTrain->getDisponibilite()
        );
        $this->assertEquals(
            '2 heures',
            $serviceVisitePetitTrain->getDuree()
        );
        $this->assertInstanceOf(
            \DateTimeImmutable::class,
            $serviceVisitePetitTrain->getCreatedAt()
        );
        $this->assertInstanceOf(
            \DateTimeImmutable::class,
            $serviceVisitePetitTrain->getUpdatedAt()
        );

        // Vérifier la relation avec le service
        $this->assertSame(
            $service,
            $serviceVisitePetitTrain->getService()
        );
    }

    public function testServiceVisitePetitTrainWithNullDates()
    {
        // Créer une instance de ServiceVisitePetitTrain sans dates initiales
        $serviceVisitePetitTrain = new ServiceVisitePetitTrain();

        // Vérifier que les dates sont nulles
        $this->assertNull(
            $serviceVisitePetitTrain->getCreatedAt()
        );
        $this->assertNull(
            $serviceVisitePetitTrain->getUpdatedAt()
        );
    }

    public function testSetAndGetService()
    {
        // Créer une instance de ServiceVisitePetitTrain et Service
        $serviceVisitePetitTrain = new ServiceVisitePetitTrain();
        $service = new Service();  // Assurez-vous d'avoir l'entité Service correctement définie

        // Définir le service pour le service visite petit train
        $serviceVisitePetitTrain->setService($service);

        // Vérifier que le service est correctement assigné
        $this->assertSame(
            $service,
            $serviceVisitePetitTrain->getService()
        );
    }
}
