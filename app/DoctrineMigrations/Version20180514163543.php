<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 * Add one new organization from Kenya
 */
final class Version20180514163543 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // fix column name typo
        $this->addSql('ALTER TABLE `config` CHANGE `is_signup_pertaners_enabled` `is_signup_partners_enabled` TINYINT(1) DEFAULT \'0\' NOT NULL');

        // insert new organization data
        $this->addSql("INSERT INTO `organizations` (`id`, `subdomain`, `country`, `name`, `is_accepted`, `createdAt`, `updatedAt`, `locales`, `required_locales`, `default_locale`, `contact_email`) VALUES (3, 'kenya', 'Kenya', 'Tech Leaders Africa', '1', '2015-09-01 00:00:00', '2018-03-19 00:00:00', 'en,sw', 'en', 'en', 'kenya@wit.pl')");

        // first edition
        $this->addSql("INSERT INTO `edition` (`id`, `organization_id`, `name`, `position`, `createdAt`, `updatedAt`) VALUES (5, 3, 'I', 0, '2018-05-10 00:00:00', '2018-05-10 00:00:00')");
        // config row for organization
        $this->addSql('INSERT INTO `config` (`id`, `organization_id`, `is_signup_mentors_enabled`, `is_signup_partners_enabled`, `is_signup_mentees_enabled`, `is_chosen_mentees_visible`) VALUES (3, 3, 0, 0, 0, 0)');

        // first user
        $this->addSql('INSERT INTO users
(organization_id, username, username_canonical, email, email_canonical, enabled, salt, password, last_login, confirmation_token,password_requested_at, roles, name, last_name, locale, createdAt, updatedAt, invitation_id) VALUES (3, "kenya@wit.pl", "kenya@wit.pl", "kenya@wit.pl", "kenya@wit.pl", 0, "ghj", "abc", null, null, null,
   "a:1:{i:0;s:9:\"ROLE_USER\";}", null, null, "en", "2018-05-10 00:00:00", "2018-05-10 00:00:00", null)');
        // end inserting new organization data

        // insert organization data
        $this->addSql("INSERT INTO `section_config` (`organization_id`, `section`, `is_enabled`) VALUES (3, 'about', 1), (1, 'mentors', 1), (1, 'partners', 1), (1, 'rules', 1)");
    }

    public function down(Schema $schema) : void
    {
        // delete new organization data
        $this->addSql('DELETE FROM `section_config` WHERE `organization_id` = 3 LIMIT 1');
        $this->addSql('DELETE FROM `config` WHERE `organization_id` = 3 LIMIT 1');
        $this->addSql('DELETE FROM `edition` WHERE `organization_id` = 3 LIMIT 1');
        $this->addSql('DELETE FROM `users` WHERE `organization_id` = 3 LIMIT 1');
        $this->addSql('DELETE FROM `organizations` WHERE `id` = 3 LIMIT 1');
        // end deleting rows

        // bring column typo back
        $this->addSql('ALTER TABLE `config` CHANGE `is_signup_partners_enabled` `is_signup_pertaners_enabled` TINYINT(1) DEFAULT \'0\' NOT NULL');
    }
}
