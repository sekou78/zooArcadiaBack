<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250308115207 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE image_service_restaurant (image_id INT NOT NULL, service_restaurant_id INT NOT NULL, INDEX IDX_E8E934223DA5256D (image_id), INDEX IDX_E8E934226158CAEC (service_restaurant_id), PRIMARY KEY(image_id, service_restaurant_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE image_service_restaurant ADD CONSTRAINT FK_E8E934223DA5256D FOREIGN KEY (image_id) REFERENCES image (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE image_service_restaurant ADD CONSTRAINT FK_E8E934226158CAEC FOREIGN KEY (service_restaurant_id) REFERENCES service_restaurant (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE image_service_restaurant DROP FOREIGN KEY FK_E8E934223DA5256D');
        $this->addSql('ALTER TABLE image_service_restaurant DROP FOREIGN KEY FK_E8E934226158CAEC');
        $this->addSql('DROP TABLE image_service_restaurant');
    }
}
