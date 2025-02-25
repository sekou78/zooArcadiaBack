<?php

// namespace App\DataFixtures;

// use App\Entity\Service;
// use App\Entity\ServiceRestaurant;
// use Doctrine\Bundle\FixturesBundle\Fixture;
// use Doctrine\Common\DataFixtures\DependentFixtureInterface;
// use Doctrine\Persistence\ObjectManager;
// use Faker;

// class ServiceRestaurantFixtures extends Fixture implements DependentFixtureInterface
// {
//     public const SERVICE_RESTAURANT_NB_TUPLES = 5;
//     public const SERVICE_RESTAURANT_REFERENCE = "service_restaurant";

//     public function load(ObjectManager $manager): void
//     {
//         $faker = Faker\Factory::create('fr_FR');

//         for ($i = 1; $i <= self::SERVICE_RESTAURANT_NB_TUPLES; $i++) {
//             $service = $this->getReference(ServiceFixtures::SERVICE_REFERENCE . $i, ServiceFixtures::class);
//             $serviceRestaurant = (new ServiceRestaurant())
//                 ->setNom($faker->company)
//                 ->setDescription($faker->sentence())
//                 ->setService($service)

//                 ->setCreatedAt(new \DateTimeImmutable());

//             $manager->persist($serviceRestaurant);

//             $this->addReference(self::SERVICE_RESTAURANT_REFERENCE . $i, $serviceRestaurant);
//         }

//         $manager->flush();
//     }

//     public function getDependencies(): array
//     {
//         return [
//             UserFixtures::class,
//             // ServiceFixtures::class,
//         ];
//     }
// }
