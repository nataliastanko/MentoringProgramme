<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Create index on mentors and persons for searching
 */
class Version20171011212238 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE INDEX mentor_name ON mentors (name)');
        $this->addSql('CREATE INDEX mentor_last_name ON mentors (last_name)');
        $this->addSql('CREATE INDEX mentor_email ON mentors (email)');
        $this->addSql('DROP INDEX search_idx ON persons');
        $this->addSql('CREATE INDEX person_name ON persons (name)');
        $this->addSql('CREATE INDEX person_last_name ON persons (last_name)');
        $this->addSql('CREATE INDEX person_email ON persons (email)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX mentor_name ON mentors');
        $this->addSql('DROP INDEX mentor_last_name ON mentors');
        $this->addSql('DROP INDEX mentor_email ON mentors');
        $this->addSql('DROP INDEX person_name ON persons');
        $this->addSql('DROP INDEX person_last_name ON persons');
        $this->addSql('DROP INDEX person_email ON persons');
        $this->addSql('CREATE INDEX search_idx ON persons (name, last_name, email)');
    }
}
