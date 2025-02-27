<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250227153940 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE service_animaux ADD animal_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE service_animaux ADD CONSTRAINT FK_553A4BD18E962C16 FOREIGN KEY (animal_id) REFERENCES animal (id)');
        $this->addSql('CREATE INDEX IDX_553A4BD18E962C16 ON service_animaux (animal_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE service_animaux DROP FOREIGN KEY FK_553A4BD18E962C16');
        $this->addSql('DROP INDEX IDX_553A4BD18E962C16 ON service_animaux');
        $this->addSql('ALTER TABLE service_animaux DROP animal_id');
    }
}
