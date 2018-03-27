<?php declare(strict_types = 1);

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180327042150 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE organizations ADD fbUrl VARCHAR(255) DEFAULT NULL');
        $this->addSql('UPDATE `organizations` SET `fbUrl` = \'https\:\/\/facebook.com/techleadersWIT\' where id = 1');
        $this->addSql('UPDATE `organizations` SET `fbUrl` = \'https\:\/\/facebook.com/techleadersglobal\' where id = 2');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE organizations DROP fbUrl');
    }
}
