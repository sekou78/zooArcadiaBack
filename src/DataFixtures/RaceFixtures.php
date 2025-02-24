<?php

namespace App\DataFixtures;

use App\Entity\Race;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;

class RaceFixtures extends Fixture implements DependentFixtureInterface
{
    public const RACE_NB_TUPLES = 5;
    public const RACE_REFERENCE = "race";

    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');

        for ($i = 1; $i <= self::RACE_NB_TUPLES; $i++) {
            $race = (new Race())
                ->setLabel($faker->word)
                ->setCreatedAt(new \DateTimeImmutable());

            $manager->persist($race);

            $this->addReference(self::RACE_REFERENCE . $i, $race);
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
