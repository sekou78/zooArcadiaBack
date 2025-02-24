<?php

namespace App\DataFixtures;

use App\Entity\RapportVeterinaire;
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

        for ($i = 1; $i <= self::RAPPORT_NB_TUPLES; $i++) {
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
                ->setQuantiteNourriture($faker->randomFloat(10, 0.5, 1000))
                ->setCommentaireHabitat($faker->sentence())
                // ->setAnimal($animal)
                // ->setVeterinaire($veterinaire)

                ->setCreatedAt(new \DateTimeImmutable());

            $manager->persist($rapportVeterinaire);

            $this->addReference(self::RAPPORT_REFERENCE . $i, $rapportVeterinaire);
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
