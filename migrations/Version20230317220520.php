<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230317220520 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE option_supplement DROP FOREIGN KEY FK_EC336BF9A7C41D6F');
        $this->addSql('ALTER TABLE room_option DROP FOREIGN KEY FK_1BC8C671A7C41D6F');
        $this->addSql('DROP TABLE equipments');
        $this->addSql('DROP TABLE `option`');
        $this->addSql('DROP TABLE option_supplement');
        $this->addSql('DROP TABLE room_option');
        $this->addSql('ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDE82EA2E54');
        $this->addSql('DROP INDEX UNIQ_E00CEDDE82EA2E54 ON booking');
        $this->addSql('ALTER TABLE booking ADD number VARCHAR(255) DEFAULT NULL, ADD state VARCHAR(255) DEFAULT NULL, DROP commande_id, DROP note, DROP reference, DROP status, DROP taxe_amount, DROP discount_amount, CHANGE checkin checkin DATE DEFAULT NULL, CHANGE checkout checkout DATE DEFAULT NULL, CHANGE confirmed_at confirmed_at DATE DEFAULT NULL, CHANGE cancelled_at cancelled_at DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE commande ADD booking_id INT DEFAULT NULL, CHANGE reference number VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67D3301C60 FOREIGN KEY (booking_id) REFERENCES booking (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6EEAA67D3301C60 ON commande (booking_id)');
        $this->addSql('ALTER TABLE email_verification CHANGE token token VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE equipment CHANGE description description VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE equipment_group CHANGE description description VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE payment CHANGE refunded refunded TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE promotion ADD slug VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE room ADD occupant INT DEFAULT NULL, DROP maximum_adults, DROP maximum_of_children, DROP type, DROP taxe_status, CHANGE description description VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE room_equipment CHANGE description description LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE room_gallery CHANGE name name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE room_user CHANGE name name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE settings DROP created_at, DROP updated_at, CHANGE name name VARCHAR(255) DEFAULT NULL, CHANGE email email VARCHAR(255) DEFAULT NULL, CHANGE phone phone VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE supplement CHANGE slug slug VARCHAR(255) NOT NULL, CHANGE type type INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD firstname VARCHAR(255) DEFAULT NULL, ADD lastname VARCHAR(255) DEFAULT NULL, DROP first_name, DROP last_name, CHANGE is_verified is_verified TINYINT(1) DEFAULT NULL, CHANGE subscribed_to_newsletter subscribed_to_newsletter TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE messenger_messages CHANGE queue_name queue_name VARCHAR(190) NOT NULL');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE equipments (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE `option` (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, position INT NOT NULL, enabled TINYINT(1) DEFAULT NULL, description LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE option_supplement (option_id INT NOT NULL, supplement_id INT NOT NULL, INDEX IDX_EC336BF97793FA21 (supplement_id), INDEX IDX_EC336BF9A7C41D6F (option_id), PRIMARY KEY(option_id, supplement_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE room_option (room_id INT NOT NULL, option_id INT NOT NULL, INDEX IDX_1BC8C67154177093 (room_id), INDEX IDX_1BC8C671A7C41D6F (option_id), PRIMARY KEY(room_id, option_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE option_supplement ADD CONSTRAINT FK_EC336BF9A7C41D6F FOREIGN KEY (option_id) REFERENCES `option` (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE option_supplement ADD CONSTRAINT FK_EC336BF97793FA21 FOREIGN KEY (supplement_id) REFERENCES supplement (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE room_option ADD CONSTRAINT FK_1BC8C67154177093 FOREIGN KEY (room_id) REFERENCES room (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE room_option ADD CONSTRAINT FK_1BC8C671A7C41D6F FOREIGN KEY (option_id) REFERENCES `option` (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE booking ADD commande_id INT NOT NULL, ADD note VARCHAR(255) DEFAULT NULL, ADD reference VARCHAR(255) DEFAULT NULL, ADD status VARCHAR(255) NOT NULL, ADD taxe_amount INT DEFAULT NULL, ADD discount_amount INT DEFAULT NULL, DROP number, DROP state, CHANGE checkin checkin DATETIME DEFAULT NULL, CHANGE checkout checkout DATETIME DEFAULT NULL, CHANGE confirmed_at confirmed_at DATETIME DEFAULT NULL, CHANGE cancelled_at cancelled_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDE82EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E00CEDDE82EA2E54 ON booking (commande_id)');
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67D3301C60');
        $this->addSql('DROP INDEX UNIQ_6EEAA67D3301C60 ON commande');
        $this->addSql('ALTER TABLE commande DROP booking_id, CHANGE number reference VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE email_verification CHANGE token token VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE equipment CHANGE description description LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE equipment_group CHANGE description description LONGTEXT DEFAULT NULL');
        $this->addSql('DROP INDEX IDX_75EA56E0FB7336F0 ON messenger_messages');
        $this->addSql('DROP INDEX IDX_75EA56E0E3BD61CE ON messenger_messages');
        $this->addSql('ALTER TABLE messenger_messages CHANGE queue_name queue_name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE payment CHANGE refunded refunded TINYINT(1) DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE promotion DROP slug');
        $this->addSql('ALTER TABLE room ADD maximum_of_children INT DEFAULT NULL, ADD type VARCHAR(255) NOT NULL, ADD taxe_status TINYINT(1) DEFAULT NULL, CHANGE description description LONGTEXT DEFAULT NULL, CHANGE occupant maximum_adults INT DEFAULT NULL');
        $this->addSql('ALTER TABLE room_equipment CHANGE description description VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE room_gallery CHANGE name name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE room_user CHANGE name name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE settings ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME DEFAULT NULL, CHANGE name name VARCHAR(255) NOT NULL, CHANGE email email VARCHAR(255) NOT NULL, CHANGE phone phone VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE supplement CHANGE slug slug VARCHAR(100) NOT NULL, CHANGE type type VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD first_name VARCHAR(255) DEFAULT NULL, ADD last_name VARCHAR(255) DEFAULT NULL, DROP firstname, DROP lastname, CHANGE subscribed_to_newsletter subscribed_to_newsletter TINYINT(1) NOT NULL, CHANGE is_verified is_verified TINYINT(1) NOT NULL');
    }
}
