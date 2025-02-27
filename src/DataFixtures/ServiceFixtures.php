<?php

namespace App\DataFixtures;

use App\Entity\Service;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;

class ServiceFixtures extends Fixture implements DependentFixtureInterface
{
    public const SERVICE_NB_TUPLES = 5;
    public const SERVICE_REFERENCE = "service";

    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');

        // Récupération des utilisateurs uniquement
        $utilisateurs = [];
        for ($i = 1; $i <= UserFixtures::User_NB_TUPLES; $i++) {
            $user = $this->getReference(
                UserFixtures::User_REFERENCE . $i,
                User::class
            );
            if (in_array(
                'ROLE_EMPLOYE',
                $user->getRoles()
            )) {
                $utilisateurs[] = $user;
            }
        }

        if (empty($utilisateurs)) {
            throw new \Exception(
                "Aucun utilisateur avec le rôle 
                'ROLE_EMPLOYE' trouvé dans les fixtures"
            );
        }

        for ($i = 1; $i <= self::SERVICE_NB_TUPLES; $i++) {
            $employe = $faker->randomElement($utilisateurs);

            $service = (new Service())
                ->setNom($faker->word)
                ->setDescription($faker->sentence())
                ->addUtilisateur($employe)

                ->setCreatedAt(new \DateTimeImmutable());

            $manager->persist($service);

            $this->addReference(self::SERVICE_REFERENCE . $i, $service);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
