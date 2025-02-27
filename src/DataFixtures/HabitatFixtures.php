<?php

namespace App\DataFixtures;

use App\Entity\Habitat;
use App\Entity\Service;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;

class HabitatFixtures extends Fixture implements DependentFixtureInterface
{
    public const HABITAT_NB_TUPLES = 5;
    public const HABITAT_REFERENCE = "habitat";

    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');

        for ($i = 1; $i <= self::HABITAT_NB_TUPLES; $i++) {
            $habitat = (new Habitat())
                ->setName($faker->word)
                ->setDescription($faker->sentence())
                ->setCommentHabitat($faker->sentence())
                ->setService($this->getReference(
                    ServiceFixtures::SERVICE_REFERENCE . $i,
                    Service::class
                ))

                ->setCreatedAt(new \DateTimeImmutable());

            $manager->persist($habitat);

            $this->addReference(
                self::HABITAT_REFERENCE . $i,
                $habitat
            );
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            ServiceFixtures::class,
            // ImageFixtures::class,
            // RapportVeterinaireFixtures::class,
        ];
    }
}
