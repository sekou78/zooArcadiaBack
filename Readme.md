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
        - $ php bin/console database:doctrine:create
        - $php bin/console doctrine:migrations:migrate

# Teste de l'application

    * Installation du pack teste
        -$ composer require --dev symfony/test-pack
    * Teste Unitaire
        -$ composer require --dev phpunit/phpunit
            Exécutez le test avec: php bin/phpunit

# Création de la BDD Tests

    * Mise en place du fichier .env.test.local et paramétrage
        - $ cp .env .env.test.local
        - $ php bin/console doctrine:database:create --env=test
        - $ php bin/console doctrine:migrations:migrate --env=test
