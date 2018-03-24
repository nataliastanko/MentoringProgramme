<?php declare(strict_types = 1);

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180324050921 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE organizations ADD locales TEXT NOT NULL COMMENT \'(DC2Type:simple_array)\', ADD default_locale VARCHAR(5) NOT NULL, ADD required_locales TEXT NOT NULL COMMENT \'(DC2Type:simple_array)\'');
        $this->addSql('UPDATE `organizations` SET `locales` = \'en,pl\', `required_locales` = \'en,pl\', `default_locale` = \'en\'  where id = 1');
        $this->addSql('UPDATE `organizations` SET `locales` = \'en,ru\', `required_locales` = \'en\', `default_locale` = \'en\' where id = 2');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE organizations DROP locale');
    }
}
