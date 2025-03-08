<?php

namespace App\DataFixtures;

use App\Entity\Animal;
use App\Entity\ServiceAnimaux;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;

class ServiceAnimauxFixtures extends Fixture implements DependentFixtureInterface
{
    public const SERVICE_ANIMAUX_NB_TUPLES = 5;
    public const SERVICE_ANIMAUX_REFERENCE = "service_animaux";

    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');

        // Récupération des employés uniquement
        $employes = [];
        for ($i = 1; $i <= UserFixtures::User_NB_TUPLES; $i++) {
            $user = $this->getReference(
                UserFixtures::User_REFERENCE . $i,
                User::class
            );
            if (in_array(
                'ROLE_EMPLOYE',
                $user->getRoles()
            )) {
                $employes[] = $user;
            }
        }

        if (empty($employes)) {
            throw new \Exception("Aucun utilisateur avec le rôle 'ROLE_EMPLOYE' trouvé dans les fixtures");
        }

        for ($i = 1; $i <= self::SERVICE_ANIMAUX_NB_TUPLES; $i++) {
            $employe = $faker->randomElement($employes);

            $service = (new ServiceAnimaux())
                ->setNom($faker->word)
                ->setDescription($faker->text(100))
                ->setNourriture($faker->word)
                ->setQuantite($faker->randomFloat(5, 0.5, 1000))
                ->setDateHeure(new \DateTimeImmutable())
                ->setAnimal($this->getReference(
                    AnimalFixtures::ANIMAL_REFERENCE . $i,
                    Animal::class
                ))
                ->addUser($employe)

                ->setCreatedAt(new \DateTimeImmutable());

            $manager->persist($service);

            $this->addReference(
                self::SERVICE_ANIMAUX_REFERENCE . $i,
                $service
            );
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            AnimalFixtures::class,
        ];
    }
}
