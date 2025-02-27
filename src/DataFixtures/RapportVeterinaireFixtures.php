<?php

namespace App\DataFixtures;

use App\Entity\Animal;
use App\Entity\RapportVeterinaire;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;

class RapportVeterinaireFixtures extends Fixture implements DependentFixtureInterface
{
    public const RAPPORT_NB_TUPLES = 5;
    public const RAPPORT_REFERENCE = "rapportVeterinaire";

    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');

        // Récupération des vétérinaires uniquement
        $veterinaires = [];
        for ($i = 1; $i <= UserFixtures::User_NB_TUPLES; $i++) {
            $user = $this->getReference(
                UserFixtures::User_REFERENCE . $i,
                User::class
            );
            if (in_array(
                'ROLE_VETERINAIRE',
                $user->getRoles()
            )) {
                $veterinaires[] = $user;
            }
        }

        if (empty($veterinaires)) {
            throw new \Exception(
                "Aucun utilisateur avec le rôle 'ROLE_VETERINAIRE' 
                trouvé dans les fixtures"
            );
        }

        for ($i = 1; $i <= self::RAPPORT_NB_TUPLES; $i++) {
            $veterinaire = $faker->randomElement($veterinaires);

            $rapportVeterinaire = (new RapportVeterinaire())
                ->setDate(new \DateTimeImmutable())
                ->setEtat($faker->randomElement(
                    [
                        'malade',
                        'en bonne santé',
                        'en observation'
                    ]
                ))
                ->setNourritureProposee($faker->word)
                ->setQuantiteNourriture($faker->randomFloat(1, 0.5, 1000))
                ->setCommentaireHabitat($faker->sentence())
                ->setAnimal($this->getReference(
                    AnimalFixtures::ANIMAL_REFERENCE . $i,
                    Animal::class
                ))
                ->setVeterinaire($veterinaire)

                ->setCreatedAt(new \DateTimeImmutable());

            $manager->persist($rapportVeterinaire);
            $this->addReference(
                self::RAPPORT_REFERENCE . $i,
                $rapportVeterinaire
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
