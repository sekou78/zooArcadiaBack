<?php

namespace App\Tests\Entity;

use App\Entity\ServiceAnimaux;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class ServiceAnimauxTest extends TestCase
{
    public function testServiceAnimauxCreation()
    {
        // Créez une instance de ServiceAnimaux
        $serviceAnimaux = new ServiceAnimaux();

        // Définir les propriétés
        $serviceAnimaux->setNomAnimal('Chien')
            ->setDescription('Un chien sympathique.')
            ->setNourriture('Croquettes')
            ->setQuantite(2.5)
            ->setDateHeure(new \DateTime())
            ->setCreatedAt(new \DateTimeImmutable())
            ->setUpdatedAt(new \DateTimeImmutable());

        // Vérifiez que les propriétés sont correctement définies
        $this->assertEquals(
            'Chien',
            $serviceAnimaux->getNomAnimal()
        );
        $this->assertEquals(
            'Un chien sympathique.',
            $serviceAnimaux->getDescription()
        );
        $this->assertEquals(
            'Croquettes',
            $serviceAnimaux->getNourriture()
        );
        $this->assertEquals(
            2.5,
            $serviceAnimaux->getQuantite()
        );
        $this->assertInstanceOf(
            \DateTimeInterface::class,
            $serviceAnimaux->getDateHeure()
        );
        $this->assertInstanceOf(
            \DateTimeImmutable::class,
            $serviceAnimaux->getCreatedAt()
        );
        $this->assertInstanceOf(
            \DateTimeImmutable::class,
            $serviceAnimaux->getUpdatedAt()
        );
    }

    public function testServiceAnimauxWithNullDates()
    {
        // Créez une instance de ServiceAnimaux sans dates initiales
        $serviceAnimaux = new ServiceAnimaux();

        // Vérifiez que les dates sont nulles
        $this->assertNull(
            $serviceAnimaux->getCreatedAt()
        );
        $this->assertNull(
            $serviceAnimaux->getUpdatedAt()
        );
        $this->assertNull(
            $serviceAnimaux->getDateHeure()
        );
    }

    public function testAddAndRemoveUser()
    {
        // Créez des instances de ServiceAnimaux et User
        $serviceAnimaux = new ServiceAnimaux();
        $user = new User(); // Assurez-vous d'avoir l'entité User correctement définie

        // Ajoutez un utilisateur au serviceAnimaux
        $serviceAnimaux->addUser($user);

        // Vérifiez que l'utilisateur a bien été ajouté
        $this->assertTrue(
            $serviceAnimaux
                ->getUsers()
                ->contains($user)
        );

        // Retirez l'utilisateur du serviceAnimaux
        $serviceAnimaux->removeUser($user);

        // Vérifiez que l'utilisateur a bien été retiré
        $this->assertFalse(
            $serviceAnimaux
                ->getUsers()
                ->contains($user)
        );
    }
}
