<?php

namespace App\DataFixtures;

use App\Entity\Animal;
use App\Entity\Habitat;
use App\Entity\Image;
use App\Entity\Service;
use App\Entity\ServiceRestaurant;
use App\Entity\ServiceVisitePetitTrain;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

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
                ->setAnimal($this->getReference(
                    AnimalFixtures::ANIMAL_REFERENCE . $i,
                    Animal::class
                ))
                ->addHabitat($this->getReference(
                    HabitatFixtures::HABITAT_REFERENCE . $i,
                    Habitat::class
                ))
                ->setService($this->getReference(
                    ServiceFixtures::SERVICE_REFERENCE . $i,
                    Service::class
                ))
                ->setUser($this->getReference(
                    UserFixtures::User_REFERENCE . $i,
                    User::class
                ))
                ->addServiceRestaurant($this->getReference(
                    ServiceRestaurantFixtures::SERVICE_RESTAURANT_REFERENCE . $i,
                    ServiceRestaurant::class
                ))
                ->addServiceVisitePetitTrain($this->getReference(
                    ServiceVisitePetitTrainFixtures::SERVICE_VISITE_PETIT_TRAIN_REFERENCE . $i,
                    ServiceVisitePetitTrain::class
                ))

                ->setCreatedAt(new \DateTimeImmutable());

            $manager->persist($image);

            $this->addReference(
                self::IMAGE_REFERENCE . $i,
                $image
            );
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            AnimalFixtures::class,
            // HabitatFixtures::class,
            // ServiceFixtures::class,
            // ServiceRestaurantFixtures::class,
            // ServiceVisitePetitTrainFixtures::class,
        ];
    }
}
