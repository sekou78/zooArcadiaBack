<?php

namespace App\DataFixtures;

use App\Entity\Image;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;

class ImageFixtures extends Fixture implements DependentFixtureInterface
{
    public const IMAGE_NB_TUPLES = 5;
    public const IMAGE_REFERENCE = "image";

    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');

        for ($i = 1; $i <= self::IMAGE_NB_TUPLES; $i++) {
            $image = (new Image())
                ->setImageData($faker->imageUrl())
                ->setFilePath($faker->imageUrl())
                // ->setAnimal($animal)
                // ->setHabitat($habitat)

                ->setCreatedAt(new \DateTimeImmutable());

            $manager->persist($image);

            $this->addReference(self::IMAGE_REFERENCE . $i, $image);
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
