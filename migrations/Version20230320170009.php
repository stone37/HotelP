<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230320170009 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE equipment_value (id INT AUTO_INCREMENT NOT NULL, equipment_id INT NOT NULL, value VARCHAR(255) DEFAULT NULL, INDEX IDX_F42E79D0517FE9FE (equipment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE equipment_value ADD CONSTRAINT FK_F42E79D0517FE9FE FOREIGN KEY (equipment_id) REFERENCES equipment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE equipment DROP FOREIGN KEY FK_D338D5837A45358C');
        $this->addSql('DROP INDEX IDX_D338D5837A45358C ON equipment');
        $this->addSql('ALTER TABLE equipment ADD slug VARCHAR(100) NOT NULL, DROP groupe_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE equipment_value');
        $this->addSql('ALTER TABLE equipment ADD groupe_id INT NOT NULL, DROP slug');
        $this->addSql('ALTER TABLE equipment ADD CONSTRAINT FK_D338D5837A45358C FOREIGN KEY (groupe_id) REFERENCES equipment_group (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_D338D5837A45358C ON equipment (groupe_id)');
    }
}
