# Instructions installation

    * Project requirements
        - PHP >=8.2.12 ou supérieur
        - SQL >=8.0
        - Symfony CLI
        - Composer

# Pour vérifier les exigences minimales pour le projet

    * $ symfony check:requirements

# Mise à jour du projet

    *Mise à jour des dépendances du projet
        - $ composer update

# Lancement du serveur local symfony

    * $ symfony server:start

# Création de la BDD

    * Mise en place du fichier .env.local et paramétrage
        - $ cp .env .env.local
        - $ php bin/console database:doctrine:create (php bin/console d:d:c)
        - $ php bin/console doctrine:migrations:migrate (php bin/console d:m:m)

# Teste de l'application

    * Installation du pack teste
        -$ composer require --dev symfony/test-pack
    * Teste Unitaire
        -$ composer require --dev phpunit/phpunit
            Exécutez le test avec: php bin/phpunit

# Création de la BDD Tests

    * Mise en place du fichier .env.test.local et paramétrage
        - $ cp .env .env.test.local
        - $ php bin/console doctrine:database:create --env=test (php bin/console d:d:c --env=test)
        - $ php bin/console doctrine:migrations:migrate --env=test (php bin/console d:m:m --env=test)

# Teste de l'application avec des fixtures

    * Mise en place des composants pour les fixtures
        - $ composer require --dev orm-fixtures
    *Creation et parametrage de la BDD
        - $ php bin/console database:doctrine:create (php bin/console d:d:c)
        - $ php bin/console doctrine:migrations:migrate (php bin/console d:m:m)
        - $ php bin/console doctrine:fixtures:load (php bin/console d:f:l)
    * Mise en place de Faker
        - $ composer require fakerphp/faker
        - $ php bin/console doctrine:fixtures:load (php bin/console d:f:l)
