<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250109161844 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE service (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(50) DEFAULT NULL, description VARCHAR(250) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE service_user (service_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_43D062A5ED5CA9E6 (service_id), INDEX IDX_43D062A5A76ED395 (user_id), PRIMARY KEY(service_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE service_user ADD CONSTRAINT FK_43D062A5ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE service_user ADD CONSTRAINT FK_43D062A5A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user ADD role_id INT NOT NULL, ADD rapports_veterinaires_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649D60322AC FOREIGN KEY (role_id) REFERENCES role (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6496234CF27 FOREIGN KEY (rapports_veterinaires_id) REFERENCES rapport_veterinaire (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649D60322AC ON user (role_id)');
        $this->addSql('CREATE INDEX IDX_8D93D6496234CF27 ON user (rapports_veterinaires_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE service_user DROP FOREIGN KEY FK_43D062A5ED5CA9E6');
        $this->addSql('ALTER TABLE service_user DROP FOREIGN KEY FK_43D062A5A76ED395');
        $this->addSql('DROP TABLE service');
        $this->addSql('DROP TABLE service_user');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649D60322AC');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6496234CF27');
        $this->addSql('DROP INDEX IDX_8D93D649D60322AC ON user');
        $this->addSql('DROP INDEX IDX_8D93D6496234CF27 ON user');
        $this->addSql('ALTER TABLE user DROP role_id, DROP rapports_veterinaires_id');
    }
}
