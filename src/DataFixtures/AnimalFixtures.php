<?php

namespace App\DataFixtures;

use App\Entity\Animal;
use App\Entity\Habitat;
use App\Entity\Race;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;

class AnimalFixtures extends Fixture implements DependentFixtureInterface
{
    public const ANIMAL_REFERENCE = "animal";
    public const ANIMAL_NB_TUPLES = 5;

    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');

        for ($i = 1; $i <= self::ANIMAL_NB_TUPLES; $i++) {
            $animal = (new Animal())
                ->setFirstname($faker->firstName)
                ->setEtat(
                    $faker->randomElement(
                        [
                            'malade',
                            'en bonne santé',
                            'en observation'
                        ]
                    )
                )
                ->setHabitat($this->getReference(
                    HabitatFixtures::HABITAT_REFERENCE . $i,
                    Habitat::class
                ))
                ->setRace($this->getReference(
                    RaceFixtures::RACE_REFERENCE . $i,
                    Race::class
                ))

                ->setCreatedAt(new \DateTimeImmutable());

            $manager->persist($animal);

            $this->addReference(
                self::ANIMAL_REFERENCE . $i,
                $animal
            );
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            HabitatFixtures::class,
            RaceFixtures::class,
        ];
    }
}
