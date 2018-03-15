<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Implement editions.
 * Create first edition.
 * Make mentors and partners depended from edition.
 */
class Version20160731100456 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE mentors_editions (mentor_id INT NOT NULL, edition_id INT NOT NULL, INDEX IDX_2F9397A0DB403044 (mentor_id), INDEX IDX_2F9397A074281A5E (edition_id), PRIMARY KEY(mentor_id, edition_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE partners_editions (partner_id INT NOT NULL, edition_id INT NOT NULL, INDEX IDX_C6D86ECC9393F8FE (partner_id), INDEX IDX_C6D86ECC74281A5E (edition_id), PRIMARY KEY(partner_id, edition_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE edition (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, position INT NOT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE mentors_editions ADD CONSTRAINT FK_2F9397A0DB403044 FOREIGN KEY (mentor_id) REFERENCES mentors (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE mentors_editions ADD CONSTRAINT FK_2F9397A074281A5E FOREIGN KEY (edition_id) REFERENCES edition (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE partners_editions ADD CONSTRAINT FK_C6D86ECC9393F8FE FOREIGN KEY (partner_id) REFERENCES partners (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE partners_editions ADD CONSTRAINT FK_C6D86ECC74281A5E FOREIGN KEY (edition_id) REFERENCES edition (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE persons ADD edition_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE persons ADD CONSTRAINT FK_A25CC7D374281A5E FOREIGN KEY (edition_id) REFERENCES edition (id)');
        $this->addSql('CREATE INDEX IDX_A25CC7D374281A5E ON persons (edition_id)');

        // add edition data
        $this->addSql("INSERT INTO `edition` (`id`, `name`, `position`, `createdAt`, `updatedAt`) VALUES (1, 'I', '0', '2016-07-31 00:00:00', '2016-07-31 00:00:00'), (2, 'II', '1', '2016-07-31 00:00:00', '2016-07-31 00:00:00')");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        // rm edition data
        $this->addSql('DELETE FROM `edition` WHERE `edition`.`id` = 1');
        $this->addSql('DELETE FROM `edition` WHERE `edition`.`id` = 2');

        $this->addSql('ALTER TABLE persons DROP FOREIGN KEY FK_A25CC7D374281A5E');
        $this->addSql('ALTER TABLE mentors_editions DROP FOREIGN KEY FK_2F9397A074281A5E');
        $this->addSql('ALTER TABLE partners_editions DROP FOREIGN KEY FK_C6D86ECC74281A5E');
        $this->addSql('DROP TABLE mentors_editions');
        $this->addSql('DROP TABLE partners_editions');
        $this->addSql('DROP TABLE edition');
        $this->addSql('DROP INDEX IDX_A25CC7D374281A5E ON persons');
        $this->addSql('ALTER TABLE persons DROP edition_id');
    }
}
