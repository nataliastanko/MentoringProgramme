<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Prepare users to be joined with mentees (persons)
 */
class Version20160926220643 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE edition CHANGE name name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE mentors CHANGE name name VARCHAR(255) NOT NULL, CHANGE last_name last_name VARCHAR(255) DEFAULT NULL, CHANGE email email VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE persons ADD user_id INT DEFAULT NULL, ADD video_url VARCHAR(255) DEFAULT NULL, CHANGE name name VARCHAR(255) NOT NULL, CHANGE last_name last_name VARCHAR(255) DEFAULT NULL, CHANGE email email VARCHAR(255) DEFAULT NULL, CHANGE education education VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE persons ADD CONSTRAINT FK_A25CC7D3A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A25CC7D3A76ED395 ON persons (user_id)');
        $this->addSql('ALTER TABLE users CHANGE name name VARCHAR(255) DEFAULT NULL, CHANGE last_name last_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE events CHANGE startTime startTime DATETIME NOT NULL, CHANGE endTime endTime DATETIME NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE edition CHANGE name name VARCHAR(100) NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE events CHANGE startTime startTime DATETIME NOT NULL, CHANGE endTime endTime DATETIME NOT NULL');
        $this->addSql('ALTER TABLE mentors CHANGE name name VARCHAR(100) NOT NULL COLLATE utf8_unicode_ci, CHANGE last_name last_name VARCHAR(100) DEFAULT NULL COLLATE utf8_unicode_ci, CHANGE email email VARCHAR(100) DEFAULT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE persons DROP FOREIGN KEY FK_A25CC7D3A76ED395');
        $this->addSql('DROP INDEX UNIQ_A25CC7D3A76ED395 ON persons');
        $this->addSql('ALTER TABLE persons DROP user_id, DROP video_url, CHANGE name name VARCHAR(100) NOT NULL COLLATE utf8_unicode_ci, CHANGE last_name last_name VARCHAR(100) DEFAULT NULL COLLATE utf8_unicode_ci, CHANGE email email VARCHAR(100) NOT NULL COLLATE utf8_unicode_ci, CHANGE education education VARCHAR(100) NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE users CHANGE name name VARCHAR(100) DEFAULT NULL COLLATE utf8_unicode_ci, CHANGE last_name last_name VARCHAR(100) DEFAULT NULL COLLATE utf8_unicode_ci');
    }
}
