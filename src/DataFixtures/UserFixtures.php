<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker;

class UserFixtures extends Fixture
{
    public const User_NB_TUPLES = 5;
    public const User_REFERENCE = 'user';

    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');
        $adminCreated = false; // Permet de s'assurer qu'un seul admin est créé

        for ($i = 1; $i <= self::User_NB_TUPLES; $i++) {
            $user = (new User())
                ->setEmail($faker->unique()->email)
                ->setUsername($faker->userName)
                ->setNom($faker->lastName)
                ->setPrenom($faker->firstName);

            // S'assurer qu'il y a un seul ADMIN
            if (!$adminCreated) {
                $user->setRoles(['ROLE_ADMIN']);
                $adminCreated = true;
            } else {
                $user->setRoles(
                    [$faker->randomElement(
                        [
                            'ROLE_EMPLOYE',
                            'ROLE_VETERINAIRE'
                        ]
                    )]
                );
            }

            $user->setPassword(
                $this->passwordHasher
                    ->hashPassword(
                        $user,
                        "Azerty$12"
                    )
            );

            $manager->persist($user);

            $this->addReference(
                self::User_REFERENCE . $i,
                $user
            );
        }

        $manager->flush();
    }
}
