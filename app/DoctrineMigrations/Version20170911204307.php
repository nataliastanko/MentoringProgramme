<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Make questions and answers work with both person (mentee) and mentor
 */
class Version20170911204307 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE answers DROP FOREIGN KEY FK_50D0C606217BBB47');

        $this->addSql('ALTER TABLE answers CHANGE person_id person_id INT DEFAULT NULL');

        $this->addSql('ALTER TABLE answers ADD mentor_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE answers ADD CONSTRAINT FK_50D0C606217BBB47 FOREIGN KEY (person_id) REFERENCES persons (id)');
        $this->addSql('ALTER TABLE answers ADD CONSTRAINT FK_50D0C606DB403044 FOREIGN KEY (mentor_id) REFERENCES mentors (id)');
        $this->addSql('CREATE INDEX IDX_50D0C606DB403044 ON answers (mentor_id)');

        $this->addSql('ALTER TABLE mentors ADD company VARCHAR(255) DEFAULT NULL');

        $this->addSql('ALTER TABLE questions ADD type VARCHAR(255) DEFAULT \'mentee\' NOT NULL');
        $this->addSql('CREATE INDEX type_idx ON questions (type)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX type_idx ON questions');
        $this->addSql('ALTER TABLE questions DROP type');

        $this->addSql('ALTER TABLE mentors DROP company');

        $this->addSql('ALTER TABLE answers DROP FOREIGN KEY FK_50D0C606217BBB47');
        $this->addSql('ALTER TABLE answers DROP FOREIGN KEY FK_50D0C606DB403044');
        $this->addSql('DROP INDEX IDX_50D0C606DB403044 ON answers');
        $this->addSql('ALTER TABLE answers DROP mentor_id');

        $this->addSql('ALTER TABLE answers CHANGE person_id person_id INT NOT NULL');

        $this->addSql('ALTER TABLE answers ADD CONSTRAINT FK_50D0C606217BBB47 FOREIGN KEY (person_id) REFERENCES persons (id)');

        $this->addSql('ALTER TABLE mentors ADD company VARCHAR(255) NOT NULL');

    }
}
