<?php

namespace App\DataFixtures;

use App\Entity\ServiceVisitePetitTrain;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;

class ServiceVisitePetitTrainFixtures extends Fixture implements DependentFixtureInterface
{
    public const SERVICE_VISITE_PETIT_TRAIN_NB_TUPLES = 5;
    public const SERVICE_VISITE_PETIT_TRAIN_REFERENCE = "serviceVisitePetitTrain";

    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');

        for ($i = 1; $i <= self::SERVICE_VISITE_PETIT_TRAIN_NB_TUPLES; $i++) {
            $serviceVisitePetitTrain = (new ServiceVisitePetitTrain())
                ->setParcours($faker->sentence(5))
                ->setDescription($faker->sentence())
                ->setDisponibilite(array('lundi', 'jeudi', 'vendredi'))
                ->setDuree("45min")
                // ->setService($service)

                ->setCreatedAt(new \DateTimeImmutable());

            $manager->persist($serviceVisitePetitTrain);

            $this->addReference(self::SERVICE_VISITE_PETIT_TRAIN_REFERENCE . $i, $serviceVisitePetitTrain);
        }

        $manager->flush();
    }
    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            ServiceFixtures::class,
        ];
    }
}
