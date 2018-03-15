<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Create invitation object identifier
 * that connects users profile with mentor or person profile
 */
class Version20171110010139 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE mentors ADD invitation_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE mentors ADD CONSTRAINT FK_7AE525BAA35D7AF0 FOREIGN KEY (invitation_id) REFERENCES invitations (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7AE525BAA35D7AF0 ON mentors (invitation_id)');
        $this->addSql('ALTER TABLE persons ADD invitation_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE persons ADD CONSTRAINT FK_A25CC7D3A35D7AF0 FOREIGN KEY (invitation_id) REFERENCES invitations (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A25CC7D3A35D7AF0 ON persons (invitation_id)');
        $this->addSql('ALTER TABLE invitations ADD is_sent TINYINT(1) DEFAULT \'0\' NOT NULL, ADD is_accepted TINYINT(1) DEFAULT \'0\' NOT NULL, DROP sent, DROP accepted');
        $this->addSql('CREATE INDEX invitation_role ON invitations (role)');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E9217BBB47');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E9DB403044');
        $this->addSql('DROP INDEX UNIQ_1483A5E9217BBB47 ON users');
        $this->addSql('DROP INDEX UNIQ_1483A5E9DB403044 ON users');
        $this->addSql('ALTER TABLE users DROP person_id, DROP mentor_id');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX invitation_role ON invitations');
        $this->addSql('ALTER TABLE invitations ADD sent TINYINT(1) NOT NULL, ADD accepted TINYINT(1) NOT NULL, DROP is_sent, DROP is_accepted');
        $this->addSql('ALTER TABLE mentors DROP FOREIGN KEY FK_7AE525BAA35D7AF0');
        $this->addSql('DROP INDEX UNIQ_7AE525BAA35D7AF0 ON mentors');
        $this->addSql('ALTER TABLE mentors DROP invitation_id');
        $this->addSql('ALTER TABLE persons DROP FOREIGN KEY FK_A25CC7D3A35D7AF0');
        $this->addSql('DROP INDEX UNIQ_A25CC7D3A35D7AF0 ON persons');
        $this->addSql('ALTER TABLE persons DROP invitation_id');
        $this->addSql('ALTER TABLE users ADD person_id INT DEFAULT NULL, ADD mentor_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E9217BBB47 FOREIGN KEY (person_id) REFERENCES persons (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E9DB403044 FOREIGN KEY (mentor_id) REFERENCES mentors (id) ON DELETE SET NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9217BBB47 ON users (person_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9DB403044 ON users (mentor_id)');
    }
}
