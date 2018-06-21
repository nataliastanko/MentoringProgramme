<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 * Add one new organization from San Francisco
 */
final class Version20180612144443 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // insert new organization data
        $this->addSql("INSERT INTO `organizations` (`id`, `subdomain`, `country`, `name`, `is_accepted`, `createdAt`, `updatedAt`, `locales`, `required_locales`, `default_locale`, `contact_email`) VALUES (4, 'sf', 'San Francisco', 'Tech Leaders San Francisco', '1', '2018-06-12 00:00:00', '2018-06-12 00:00:00', 'en', 'en', 'en', 'techleaders@womenintechnology.pl'), (5, 'india', 'India', 'Tech Leaders India', '1', '2018-06-12 00:00:00', '2018-06-12 00:00:00', 'en,hi', 'en', 'en', 'techleaders@womenintechnology.pl')");

        // first edition
        $this->addSql("INSERT INTO `edition` (`organization_id`, `name`, `position`, `createdAt`, `updatedAt`) VALUES (4, 'I', 0, '2018-06-12 00:00:00', '2018-06-12 00:00:00'), (5, 'I', 0, '2018-06-12 00:00:00', '2018-06-12 00:00:00')");
        // config row for organization
        $this->addSql('INSERT INTO `config` (`id`, `organization_id`, `is_signup_mentors_enabled`, `is_signup_partners_enabled`, `is_signup_mentees_enabled`, `is_chosen_mentees_visible`) VALUES (4, 4, 0, 0, 0, 0), (5, 5, 1, 1, 0, 0)');

        // first user
        $this->addSql('INSERT INTO users
(organization_id, username, username_canonical, email, email_canonical, enabled, salt, password, last_login, confirmation_token,password_requested_at, roles, name, last_name, locale, createdAt, updatedAt, invitation_id) VALUES (4, "sf@wit.pl", "sf@wit.pl", "sf@wit.pl", "sf@wit.pl", 0, "ghj", "abc", null, null, null,
   "a:1:{i:0;s:9:\"ROLE_USER\";}", null, null, "en", "2018-06-12 00:00:00", "2018-06-12 00:00:00", null), (5, "india@wit.pl", "india@wit.pl", "india@wit.pl", "india@wit.pl", 0, "ghj", "abc", null, null, null,
   "a:1:{i:0;s:9:\"ROLE_USER\";}", null, null, "en", "2018-06-12 00:00:00", "2018-06-12 00:00:00", null)');
        // end inserting new organization data

        // insert organization data
        $this->addSql("INSERT INTO `section_config` (`organization_id`, `section`, `is_enabled`) VALUES (4, 'about', 1), (4, 'mentors', 1), (4, 'partners', 1), (4, 'rules', 1), (5, 'about', 1), (5, 'mentors', 1), (5, 'partners', 1), (5, 'rules', 1)");
    }

    public function down(Schema $schema) : void
    {
        // delete new organization data
        $this->addSql('DELETE FROM `section_config` WHERE `organization_id` = 4');
        $this->addSql('DELETE FROM `section_config` WHERE `organization_id` = 5');
        $this->addSql('DELETE FROM `config` WHERE `organization_id` = 4');
        $this->addSql('DELETE FROM `config` WHERE `organization_id` = 5');
        $this->addSql('DELETE FROM `edition` WHERE `organization_id` = 4');
        $this->addSql('DELETE FROM `edition` WHERE `organization_id` = 5');
        $this->addSql('DELETE FROM `users` WHERE `organization_id` = 4');
        $this->addSql('DELETE FROM `users` WHERE `organization_id` = 5');
        $this->addSql('DELETE FROM `organizations` WHERE `id` = 4');
        $this->addSql('DELETE FROM `organizations` WHERE `id` = 5');
        // end deleting rows
    }
}
