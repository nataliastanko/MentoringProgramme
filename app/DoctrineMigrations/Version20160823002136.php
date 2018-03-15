<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Make sponsors depended from edition
 */
class Version20160823002136 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sponsors (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) DEFAULT NULL, url VARCHAR(255) DEFAULT NULL, position INT NOT NULL, description LONGTEXT DEFAULT NULL, photoName VARCHAR(255) DEFAULT NULL, deletedAt DATETIME DEFAULT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sponsors_editions (sponsor_id INT NOT NULL, edition_id INT NOT NULL, INDEX IDX_C8F8BF3612F7FB51 (sponsor_id), INDEX IDX_C8F8BF3674281A5E (edition_id), PRIMARY KEY(sponsor_id, edition_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sponsors_editions ADD CONSTRAINT FK_C8F8BF3612F7FB51 FOREIGN KEY (sponsor_id) REFERENCES sponsors (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sponsors_editions ADD CONSTRAINT FK_C8F8BF3674281A5E FOREIGN KEY (edition_id) REFERENCES edition (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX edition_name ON edition (name)');
        $this->addSql('ALTER TABLE partners ADD description LONGTEXT DEFAULT NULL, ADD deletedAt DATETIME DEFAULT NULL, CHANGE name name VARCHAR(255) NOT NULL, CHANGE email email VARCHAR(255) DEFAULT NULL');
        $this->addSql('DROP INDEX search_idx ON users');
        $this->addSql('ALTER TABLE users CHANGE username username VARCHAR(180) NOT NULL, CHANGE username_canonical username_canonical VARCHAR(180) NOT NULL, CHANGE email email VARCHAR(180) NOT NULL, CHANGE email_canonical email_canonical VARCHAR(180) NOT NULL');
        $this->addSql('CREATE INDEX user_name ON users (name)');
        $this->addSql('CREATE INDEX user_last_name ON users (last_name)');
        $this->addSql('CREATE INDEX user_email ON users (email)');
        $this->addSql('ALTER TABLE events CHANGE startTime startTime DATETIME NOT NULL, CHANGE endTime endTime DATETIME NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sponsors_editions DROP FOREIGN KEY FK_C8F8BF3612F7FB51');
        $this->addSql('DROP TABLE sponsors');
        $this->addSql('DROP TABLE sponsors_editions');
        $this->addSql('DROP INDEX edition_name ON edition');
        $this->addSql('ALTER TABLE events CHANGE startTime startTime DATETIME NOT NULL, CHANGE endTime endTime DATETIME NOT NULL');
        $this->addSql('ALTER TABLE partners DROP description, DROP deletedAt, CHANGE name name VARCHAR(100) NOT NULL COLLATE utf8_unicode_ci, CHANGE email email VARCHAR(100) DEFAULT NULL COLLATE utf8_unicode_ci');
        $this->addSql('DROP INDEX user_name ON users');
        $this->addSql('DROP INDEX user_last_name ON users');
        $this->addSql('DROP INDEX user_email ON users');
        $this->addSql('ALTER TABLE users CHANGE username username VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE username_canonical username_canonical VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE email email VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE email_canonical email_canonical VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('CREATE INDEX search_idx ON users (name, last_name, email)');
    }
}
