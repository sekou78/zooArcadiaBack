<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250308115323 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE image_service_visite_petit_train (image_id INT NOT NULL, service_visite_petit_train_id INT NOT NULL, INDEX IDX_7A6187463DA5256D (image_id), INDEX IDX_7A618746146AEAB3 (service_visite_petit_train_id), PRIMARY KEY(image_id, service_visite_petit_train_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE image_service_visite_petit_train ADD CONSTRAINT FK_7A6187463DA5256D FOREIGN KEY (image_id) REFERENCES image (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE image_service_visite_petit_train ADD CONSTRAINT FK_7A618746146AEAB3 FOREIGN KEY (service_visite_petit_train_id) REFERENCES service_visite_petit_train (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE image_service_visite_petit_train DROP FOREIGN KEY FK_7A6187463DA5256D');
        $this->addSql('ALTER TABLE image_service_visite_petit_train DROP FOREIGN KEY FK_7A618746146AEAB3');
        $this->addSql('DROP TABLE image_service_visite_petit_train');
    }
}
