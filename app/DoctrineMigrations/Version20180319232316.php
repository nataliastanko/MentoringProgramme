<?php declare(strict_types = 1);

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Add organization table
 * Add existing WiT Poland data and one new from Russia
 */
class Version20180319232316 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE organizations (id INT AUTO_INCREMENT NOT NULL, subdomain VARCHAR(15) NOT NULL, country VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, is_accepted TINYINT(1) DEFAULT \'0\' NOT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME NOT NULL, UNIQUE INDEX UNIQ_427C1C7FC1D5962E (subdomain), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');

        // insert new organization data
        $this->addSql("INSERT INTO `organizations` (`id`, `subdomain`, `country`, `name`, `is_accepted`, `createdAt`, `updatedAt`) VALUES (1, 'poland', 'Poland', 'Women in Technology', '1', '2015-09-01 00:00:00', '2018-03-19 00:00:00'), (2, 'russia', 'Russia', 'Women Techmakers Voronezh', '1', '2018-03-19 00:00:00', '2018-03-19 00:00:00')");
        // end inserting new organization data

        $this->addSql('ALTER TABLE about ADD organization_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE about ADD CONSTRAINT FK_B5F422E332C8A3DE FOREIGN KEY (organization_id) REFERENCES organizations (id)');
        // assign organization
        $this->addSql('UPDATE `about` SET `organization_id` = 1');
        $this->addSql('CREATE INDEX IDX_B5F422E332C8A3DE ON about (organization_id)');

        $this->addSql('ALTER TABLE sponsors ADD organization_id INT DEFAULT NULL');
        $this->addSql('UPDATE `sponsors` SET `organization_id` = 1');
        $this->addSql('ALTER TABLE sponsors ADD CONSTRAINT FK_9A31550F32C8A3DE FOREIGN KEY (organization_id) REFERENCES organizations (id)');
        $this->addSql('CREATE INDEX IDX_9A31550F32C8A3DE ON sponsors (organization_id)');

        $this->addSql('ALTER TABLE mentors ADD organization_id INT DEFAULT NULL');
        $this->addSql('UPDATE `mentors` SET `organization_id` = 1');
        $this->addSql('ALTER TABLE mentors ADD CONSTRAINT FK_7AE525BA32C8A3DE FOREIGN KEY (organization_id) REFERENCES organizations (id)');
        $this->addSql('CREATE INDEX IDX_7AE525BA32C8A3DE ON mentors (organization_id)');

        $this->addSql('ALTER TABLE rules ADD organization_id INT DEFAULT NULL');
        $this->addSql('UPDATE `rules` SET `organization_id` = 1');
        $this->addSql('ALTER TABLE rules ADD CONSTRAINT FK_899A993C32C8A3DE FOREIGN KEY (organization_id) REFERENCES organizations (id)');
        $this->addSql('CREATE INDEX IDX_899A993C32C8A3DE ON rules (organization_id)');

        $this->addSql('ALTER TABLE questions ADD organization_id INT DEFAULT NULL');
        $this->addSql('UPDATE `questions` SET `organization_id` = 1');
        $this->addSql('ALTER TABLE questions ADD CONSTRAINT FK_8ADC54D532C8A3DE FOREIGN KEY (organization_id) REFERENCES organizations (id)');
        $this->addSql('CREATE INDEX IDX_8ADC54D532C8A3DE ON questions (organization_id)');

        $this->addSql('ALTER TABLE partners ADD organization_id INT DEFAULT NULL');
        $this->addSql('UPDATE `partners` SET `organization_id` = 1');
        $this->addSql('ALTER TABLE partners ADD CONSTRAINT FK_EFEB516432C8A3DE FOREIGN KEY (organization_id) REFERENCES organizations (id)');
        $this->addSql('CREATE INDEX IDX_EFEB516432C8A3DE ON partners (organization_id)');

        $this->addSql('ALTER TABLE users ADD organization_id INT DEFAULT NULL');
        $this->addSql('UPDATE `users` SET `organization_id` = 1');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E932C8A3DE FOREIGN KEY (organization_id) REFERENCES organizations (id)');
        $this->addSql('CREATE INDEX IDX_1483A5E932C8A3DE ON users (organization_id)');

        $this->addSql('ALTER TABLE persons ADD organization_id INT DEFAULT NULL');
        $this->addSql('UPDATE `persons` SET `organization_id` = 1');
        $this->addSql('ALTER TABLE persons ADD CONSTRAINT FK_A25CC7D332C8A3DE FOREIGN KEY (organization_id) REFERENCES organizations (id)');
        $this->addSql('CREATE INDEX IDX_A25CC7D332C8A3DE ON persons (organization_id)');

        $this->addSql('ALTER TABLE config ADD organization_id INT DEFAULT NULL');
        $this->addSql('UPDATE `config` SET `organization_id` = 1');
        $this->addSql('ALTER TABLE config ADD CONSTRAINT FK_D48A2F7C32C8A3DE FOREIGN KEY (organization_id) REFERENCES organizations (id)');
        $this->addSql('CREATE INDEX IDX_D48A2F7C32C8A3DE ON config (organization_id)');

        $this->addSql('ALTER TABLE faq ADD organization_id INT DEFAULT NULL');
        $this->addSql('UPDATE `faq` SET `organization_id` = 1');
        $this->addSql('ALTER TABLE faq ADD CONSTRAINT FK_E8FF75CC32C8A3DE FOREIGN KEY (organization_id) REFERENCES organizations (id)');
        $this->addSql('CREATE INDEX IDX_E8FF75CC32C8A3DE ON faq (organization_id)');

        $this->addSql('ALTER TABLE edition ADD organization_id INT DEFAULT NULL');
        $this->addSql('UPDATE `edition` SET `organization_id` = 1');
        $this->addSql('ALTER TABLE edition ADD CONSTRAINT FK_A891181F32C8A3DE FOREIGN KEY (organization_id) REFERENCES organizations (id)');
        $this->addSql('CREATE INDEX IDX_A891181F32C8A3DE ON edition (organization_id)');

        $this->addSql('ALTER TABLE mentor_faq ADD organization_id INT DEFAULT NULL');
        $this->addSql('UPDATE `mentor_faq` SET `organization_id` = 1');
        $this->addSql('ALTER TABLE mentor_faq ADD CONSTRAINT FK_F241D8E632C8A3DE FOREIGN KEY (organization_id) REFERENCES organizations (id)');
        $this->addSql('CREATE INDEX IDX_F241D8E632C8A3DE ON mentor_faq (organization_id)');

        // after filling data set foreign key to be not null
        // walkaround for column that is a foreign key and nullable=false
        $this->addSql('ALTER TABLE sponsors CHANGE organization_id organization_id INT NOT NULL');
        $this->addSql('ALTER TABLE mentors CHANGE organization_id organization_id INT NOT NULL');
        $this->addSql('ALTER TABLE rules CHANGE organization_id organization_id INT NOT NULL');
        $this->addSql('ALTER TABLE questions CHANGE organization_id organization_id INT NOT NULL');
        $this->addSql('ALTER TABLE partners CHANGE organization_id organization_id INT NOT NULL');
        $this->addSql('ALTER TABLE users CHANGE organization_id organization_id INT NOT NULL');
        $this->addSql('ALTER TABLE persons CHANGE organization_id organization_id INT NOT NULL');
        $this->addSql('ALTER TABLE config CHANGE organization_id organization_id INT NOT NULL');
        $this->addSql('ALTER TABLE faq CHANGE organization_id organization_id INT NOT NULL');
        $this->addSql('ALTER TABLE about CHANGE organization_id organization_id INT NOT NULL');
        $this->addSql('ALTER TABLE edition CHANGE organization_id organization_id INT NOT NULL');
        $this->addSql('ALTER TABLE mentor_faq CHANGE organization_id organization_id INT NOT NULL');

        // add new config column
        $this->addSql('ALTER TABLE config ADD is_users_accounts_enabled TINYINT(1) DEFAULT \'0\' NOT NULL');

        // new config for current organization
        $this->addSql('UPDATE `config` SET `is_users_accounts_enabled` = 1 where organization_id = 1');

        // insert new organization data
        // (first edition)
        $this->addSql("INSERT INTO `edition` (`id`, `organization_id`, `name`, `position`, `createdAt`, `updatedAt`) VALUES (4, 2, 'I', 0, '2018-03-19 00:00:00', '2018-03-19 00:00:00')");
        // config row per organization
        $this->addSql('INSERT INTO `config` (`id`, `organization_id`, `is_signup_mentors_enabled`, `is_signup_pertaners_enabled`, `is_signup_mentees_enabled`, `is_users_accounts_enabled`) VALUES (2, 2, 0, 0, 0, 0)');

        $this->addSql('INSERT INTO users
(organization_id, username, username_canonical, email, email_canonical, enabled, salt, password, last_login, confirmation_token,password_requested_at, roles, name, last_name, locale, createdAt, updatedAt, invitation_id) VALUES (2, "russia@wit.pl", "russia@wit.pl", "russia@wit.pl", "russia@wit.pl", 0, "ghj", "abc", null, null, null,
   "a:1:{i:0;s:9:\"ROLE_USER\";}", null, null, "en", "2018-03-23 04:58:15", "2018-03-23 04:58:15", null)');
        // end inserting new organization data
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        // drop new config column
        $this->addSql('ALTER TABLE config DROP is_users_accounts_enabled');

        // delete new organization data
        $this->addSql('DELETE FROM edition WHERE id = 4 LIMIT 1');
        $this->addSql('DELETE FROM config WHERE id = 2 LIMIT 1');
        $this->addSql('DELETE FROM users WHERE email = "russia@wit.pl" LIMIT 1');
        // end deleting rows

        $this->addSql('ALTER TABLE about CHANGE organization_id organization_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE config CHANGE organization_id organization_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE edition CHANGE organization_id organization_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE faq CHANGE organization_id organization_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE mentor_faq CHANGE organization_id organization_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE mentors CHANGE organization_id organization_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE partners CHANGE organization_id organization_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE persons CHANGE organization_id organization_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE questions CHANGE organization_id organization_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE rules CHANGE organization_id organization_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sponsors CHANGE organization_id organization_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE users CHANGE organization_id organization_id INT DEFAULT NULL');

        $this->addSql('ALTER TABLE about DROP FOREIGN KEY FK_B5F422E332C8A3DE');
        $this->addSql('DROP INDEX IDX_B5F422E332C8A3DE ON about');
        $this->addSql('ALTER TABLE about DROP organization_id');
        $this->addSql('ALTER TABLE config DROP FOREIGN KEY FK_D48A2F7C32C8A3DE');
        $this->addSql('DROP INDEX IDX_D48A2F7C32C8A3DE ON config');
        $this->addSql('ALTER TABLE config DROP organization_id');
        $this->addSql('ALTER TABLE edition DROP FOREIGN KEY FK_A891181F32C8A3DE');
        $this->addSql('DROP INDEX IDX_A891181F32C8A3DE ON edition');
        $this->addSql('ALTER TABLE edition DROP organization_id');
        $this->addSql('ALTER TABLE faq DROP FOREIGN KEY FK_E8FF75CC32C8A3DE');
        $this->addSql('DROP INDEX IDX_E8FF75CC32C8A3DE ON faq');
        $this->addSql('ALTER TABLE faq DROP organization_id');
        $this->addSql('ALTER TABLE mentor_faq DROP FOREIGN KEY FK_F241D8E632C8A3DE');
        $this->addSql('DROP INDEX IDX_F241D8E632C8A3DE ON mentor_faq');
        $this->addSql('ALTER TABLE mentor_faq DROP organization_id');
        $this->addSql('ALTER TABLE mentors DROP FOREIGN KEY FK_7AE525BA32C8A3DE');
        $this->addSql('DROP INDEX IDX_7AE525BA32C8A3DE ON mentors');
        $this->addSql('ALTER TABLE mentors DROP organization_id');
        $this->addSql('ALTER TABLE partners DROP FOREIGN KEY FK_EFEB516432C8A3DE');
        $this->addSql('DROP INDEX IDX_EFEB516432C8A3DE ON partners');
        $this->addSql('ALTER TABLE partners DROP organization_id');
        $this->addSql('ALTER TABLE persons DROP FOREIGN KEY FK_A25CC7D332C8A3DE');
        $this->addSql('DROP INDEX IDX_A25CC7D332C8A3DE ON persons');
        $this->addSql('ALTER TABLE persons DROP organization_id');
        $this->addSql('ALTER TABLE questions DROP FOREIGN KEY FK_8ADC54D532C8A3DE');
        $this->addSql('DROP INDEX IDX_8ADC54D532C8A3DE ON questions');
        $this->addSql('ALTER TABLE questions DROP organization_id');
        $this->addSql('ALTER TABLE rules DROP FOREIGN KEY FK_899A993C32C8A3DE');
        $this->addSql('DROP INDEX IDX_899A993C32C8A3DE ON rules');
        $this->addSql('ALTER TABLE rules DROP organization_id');
        $this->addSql('ALTER TABLE sponsors DROP FOREIGN KEY FK_9A31550F32C8A3DE');
        $this->addSql('DROP INDEX IDX_9A31550F32C8A3DE ON sponsors');
        $this->addSql('ALTER TABLE sponsors DROP organization_id');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E932C8A3DE');
        $this->addSql('DROP INDEX IDX_1483A5E932C8A3DE ON users');
        $this->addSql('ALTER TABLE users DROP organization_id');

        $this->addSql('DROP TABLE organizations');
    }
}
