<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171118031023 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE events_participants');
        $this->addSql('ALTER TABLE events DROP FOREIGN KEY FK_5387574AA76ED395');
        $this->addSql('DROP INDEX IDX_5387574AA76ED395 ON events');
        $this->addSql('ALTER TABLE events ADD participant_id INT DEFAULT NULL, CHANGE user_id author_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE events ADD CONSTRAINT FK_5387574AF675F31B FOREIGN KEY (author_id) REFERENCES users (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE events ADD CONSTRAINT FK_5387574A9D1C3019 FOREIGN KEY (participant_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_5387574AF675F31B ON events (author_id)');
        $this->addSql('CREATE INDEX IDX_5387574A9D1C3019 ON events (participant_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE events_participants (event_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_E8FA4B6271F7E88B (event_id), INDEX IDX_E8FA4B62A76ED395 (user_id), PRIMARY KEY(event_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE events_participants ADD CONSTRAINT FK_E8FA4B6271F7E88B FOREIGN KEY (event_id) REFERENCES events (id)');
        $this->addSql('ALTER TABLE events_participants ADD CONSTRAINT FK_E8FA4B62A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE events DROP FOREIGN KEY FK_5387574AF675F31B');
        $this->addSql('ALTER TABLE events DROP FOREIGN KEY FK_5387574A9D1C3019');
        $this->addSql('DROP INDEX IDX_5387574AF675F31B ON events');
        $this->addSql('DROP INDEX IDX_5387574A9D1C3019 ON events');
        $this->addSql('ALTER TABLE events ADD user_id INT DEFAULT NULL, DROP author_id, DROP participant_id');
        $this->addSql('ALTER TABLE events ADD CONSTRAINT FK_5387574AA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_5387574AA76ED395 ON events (user_id)');
    }
}
