<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250129170046 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rapport_veterinaire ADD veterinaire_id INT DEFAULT NULL, ADD etat VARCHAR(255) DEFAULT NULL, ADD nourriture_proposee VARCHAR(255) NOT NULL, ADD quantite_nourriture DOUBLE PRECISION NOT NULL, ADD commentaire_habitat VARCHAR(255) DEFAULT NULL, DROP detail');
        $this->addSql('ALTER TABLE rapport_veterinaire ADD CONSTRAINT FK_CE729CDE5C80924 FOREIGN KEY (veterinaire_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_CE729CDE5C80924 ON rapport_veterinaire (veterinaire_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rapport_veterinaire DROP FOREIGN KEY FK_CE729CDE5C80924');
        $this->addSql('DROP INDEX IDX_CE729CDE5C80924 ON rapport_veterinaire');
        $this->addSql('ALTER TABLE rapport_veterinaire ADD detail VARCHAR(50) DEFAULT NULL, DROP veterinaire_id, DROP etat, DROP nourriture_proposee, DROP quantite_nourriture, DROP commentaire_habitat');
    }
}
