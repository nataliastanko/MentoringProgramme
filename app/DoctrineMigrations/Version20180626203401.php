<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Enable menteesExternalSignup for:
 *  - Russia 2
 *  - Kenya 3
 *  - US 4
 *  - India 5
 *  (all except Poland)
 *
 * Drop displaying chosen mentees config
 */
final class Version20180626203401 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // drop is_chosen_mentees_visible
        $this->addSql('ALTER TABLE config DROP is_chosen_mentees_visible');

        // add 2 new config cols,
        // add posibility to set external link for mentees signup
        $this->addSql('ALTER TABLE config ADD menteesExternalSignupUrl VARCHAR(255) DEFAULT NULL, ADD partners_email VARCHAR(255) DEFAULT NULL');

        // migrate partners_email data from org table
        $this->addSql('UPDATE config SET partners_email = (SELECT o.partners_email FROM organizations o WHERE organization_id = o.id)');

        // drop partners_email from org table after data migration
        $this->addSql('ALTER TABLE organizations DROP partners_email');

        // insert organization data (config sections)
        $this->addSql("INSERT INTO `section_config` (`organization_id`, `section`, `is_enabled`) VALUES (2, 'menteesExternalSignup', 1), (3, 'menteesExternalSignup', 1), (4, 'menteesExternalSignup', 1), (5, 'menteesExternalSignup', 1)");
    }

    public function down(Schema $schema) : void
    {
        // revert is_chosen_mentees_visible
        $this->addSql('ALTER TABLE config ADD is_chosen_mentees_visible TINYINT(1) DEFAULT \'0\' NOT NULL');

        // revert org table partners_email
        $this->addSql('ALTER TABLE organizations ADD partners_email VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci');

        // migrate back partners_email data to org table
        $this->addSql('UPDATE organizations SET organizations.partners_email = (SELECT c.partners_email FROM config c WHERE organizations.id = c.organization_id)');

        // drop menteesExternalSignupUrl after migrating back data
        $this->addSql('ALTER TABLE config DROP menteesExternalSignupUrl, DROP partners_email');

        // delete organization rows data (config sections)
        $this->addSql('DELETE FROM `section_config` WHERE `organization_id` = 2 AND `section` = "menteesExternalSignup"');
        $this->addSql('DELETE FROM `section_config` WHERE `organization_id` = 3 AND `section` = "menteesExternalSignup"');
        $this->addSql('DELETE FROM `section_config` WHERE `organization_id` = 4 AND `section` = "menteesExternalSignup"');
        $this->addSql('DELETE FROM `section_config` WHERE `organization_id` = 5 AND `section` = "menteesExternalSignup"');
    }
}
