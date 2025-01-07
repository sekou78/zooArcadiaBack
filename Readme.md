# Instructions installation

    * Project requirements
        - PHP >=7.2.5 ou supérieur
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
        - $ php bin/console d:d:c
