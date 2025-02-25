<?php

// namespace App\DataFixtures;

// use App\Entity\ServiceAnimaux;
// use Doctrine\Bundle\FixturesBundle\Fixture;
// use Doctrine\Common\DataFixtures\DependentFixtureInterface;
// use Doctrine\Persistence\ObjectManager;
// use Faker;

// class ServiceAnimauxFixtures extends Fixture implements DependentFixtureInterface
// {
//     public const SERVICE_ANIMAUX_NB_TUPLES = 5;
//     public const SERVICE_ANIMAUX_REFERENCE = "service_animaux";

//     public function load(ObjectManager $manager): void
//     {
//         $faker = Faker\Factory::create('fr_FR');

//         for ($i = 1; $i <= self::SERVICE_ANIMAUX_NB_TUPLES; $i++) {
//             $firstNameAnimal = $this->getReference(AnimalFixtures::ANIMAL_REFERENCE . $i, AnimalFixtures::class);
//             $service = (new ServiceAnimaux())
//                 ->setDescription($faker->text(100))
//                 ->setNourriture($faker->word)
//                 ->setQuantite($faker->randomFloat(5, 0.5, 1000))
//                 ->setDateHeure(new \DateTimeImmutable())
//                 ->setNomAnimal($firstNameAnimal->getNomAnimal())

//                 ->setCreatedAt(new \DateTimeImmutable());

//             $manager->persist($service);

//             $this->addReference(self::SERVICE_ANIMAUX_REFERENCE . $i, $service);
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
