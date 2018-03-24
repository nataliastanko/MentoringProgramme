<?php declare(strict_types = 1);

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180323222509 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE section_config (id INT AUTO_INCREMENT NOT NULL, organization_id INT NOT NULL, section VARCHAR(50) NOT NULL, is_enabled TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX IDX_9EC2DD5A32C8A3DE (organization_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE section_config ADD CONSTRAINT FK_9EC2DD5A32C8A3DE FOREIGN KEY (organization_id) REFERENCES organizations (id)');

        $this->addSql('ALTER TABLE config DROP is_users_accounts_enabled');

        // insert organization data
        // poland
        $this->addSql("INSERT INTO `section_config` (`organization_id`, `section`, `is_enabled`) VALUES (1, 'about', 1), (1, 'mentors', 1), (1, 'partners', 1), (1, 'rules', 1), (1, 'mentorsfaq', 1), (1, 'mentees', 1), (1, 'gallery', 1), (1, 'sponsors', 1),(1, 'calendar', 1)");
        // russia
        $this->addSql("INSERT INTO `section_config` (`organization_id`, `section`, `is_enabled`) VALUES (2, 'partners', 1), (2, 'about', 1), (2, 'faq', 1), (2, 'rules', 1), (2, 'sponsors', 1)");
        // end inserting new organization data
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE section_config');
        $this->addSql('ALTER TABLE config ADD is_users_accounts_enabled TINYINT(1) DEFAULT \'0\' NOT NULL');
    }
}
