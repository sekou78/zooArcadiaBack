<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250129131418 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE service_animaux_user (service_animaux_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_C606E6D03F8BD51E (service_animaux_id), INDEX IDX_C606E6D0A76ED395 (user_id), PRIMARY KEY(service_animaux_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE service_animaux_user ADD CONSTRAINT FK_C606E6D03F8BD51E FOREIGN KEY (service_animaux_id) REFERENCES service_animaux (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE service_animaux_user ADD CONSTRAINT FK_C606E6D0A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE service_animaux CHANGE quantite quantite DOUBLE PRECISION DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE service_animaux_user DROP FOREIGN KEY FK_C606E6D03F8BD51E');
        $this->addSql('ALTER TABLE service_animaux_user DROP FOREIGN KEY FK_C606E6D0A76ED395');
        $this->addSql('DROP TABLE service_animaux_user');
        $this->addSql('ALTER TABLE service_animaux CHANGE quantite quantite INT DEFAULT NULL');
    }
}
