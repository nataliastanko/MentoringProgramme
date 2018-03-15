<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Assign data to edition.
 * Migrate to first edition.
 */
class Version20160731100834 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        //assign persons to edition 1
        $this->addSql('UPDATE `persons` SET `edition_id` = 1');

        //assign mentors to edition 1
        $this->addSql('INSERT INTO `mentors_editions` (`mentor_id`, `edition_id`) SELECT m.id, 1 FROM mentors m');

        //assign partners to edition 1
        $this->addSql('INSERT INTO `partners_editions` (`partner_id`, `edition_id`) SELECT m.id, 1 FROM partners m');

        // add events table
        $this->addSql('CREATE TABLE events (id INT AUTO_INCREMENT NOT NULL, startTime DATETIME NOT NULL, endTime DATETIME NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        // drop events table
        $this->addSql('DROP TABLE events');

        //dissconnect persons from edition 1
        $this->addSql('UPDATE `persons` SET `edition_id` = NULL');

        // rm mentors_editions data
        $this->addSql('TRUNCATE `mentors_editions`');

        // rm mentpartners_editionsors_editions data
        $this->addSql('TRUNCATE `partners_editions`');
    }
}
