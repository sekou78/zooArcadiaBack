<?php

namespace App\DataFixtures;

use App\Entity\Animal;
use App\Entity\Avis;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;

class AvisFixtures extends Fixture implements DependentFixtureInterface
{
    public const AVIS_NB_TUPLES = 5;
    public const AVIS_REFERENCE = "avis";

    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');

        for ($i = 1; $i <= self::AVIS_NB_TUPLES; $i++) {
            $avis = (new Avis())
                ->setPseudo($faker->word)
                ->setComments($faker->sentence())
                ->setVisible($faker->boolean())
                ->setAnimal($this->getReference(
                    AnimalFixtures::ANIMAL_REFERENCE . $i,
                    Animal::class
                ))

                ->setCreatedAt(new \DateTimeImmutable());

            $manager->persist($avis);
            $this->addReference(
                self::AVIS_REFERENCE . $i,
                $avis
            );
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
