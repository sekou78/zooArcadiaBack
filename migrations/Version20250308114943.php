<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250308114943 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE habitat_image (habitat_id INT NOT NULL, image_id INT NOT NULL, INDEX IDX_9AD7E031AFFE2D26 (habitat_id), INDEX IDX_9AD7E0313DA5256D (image_id), PRIMARY KEY(habitat_id, image_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE habitat_image ADD CONSTRAINT FK_9AD7E031AFFE2D26 FOREIGN KEY (habitat_id) REFERENCES habitat (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE habitat_image ADD CONSTRAINT FK_9AD7E0313DA5256D FOREIGN KEY (image_id) REFERENCES image (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE habitat_image DROP FOREIGN KEY FK_9AD7E031AFFE2D26');
        $this->addSql('ALTER TABLE habitat_image DROP FOREIGN KEY FK_9AD7E0313DA5256D');
        $this->addSql('DROP TABLE habitat_image');
    }
}
