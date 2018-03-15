<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Create invitations
 */
class Version20171027125813 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE invitations (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(256) NOT NULL, email VARCHAR(256) NOT NULL, role VARCHAR(256) NOT NULL, sent TINYINT(1) NOT NULL, accepted TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE users ADD invitation_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E9A35D7AF0 FOREIGN KEY (invitation_id) REFERENCES invitations (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9A35D7AF0 ON users (invitation_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E9A35D7AF0');
        $this->addSql('DROP TABLE invitations');
        $this->addSql('DROP INDEX UNIQ_1483A5E9A35D7AF0 ON users');
        $this->addSql('ALTER TABLE users DROP invitation_id');
    }
}
