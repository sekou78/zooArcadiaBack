<?php

namespace App\DataFixtures;

use App\Entity\Service;
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

        for ($i = 1; $i <= self::SERVICE_NB_TUPLES; $i++) {
            $service = (new Service())
                ->setNom($faker->word)
                ->setDescription($faker->sentence)

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
