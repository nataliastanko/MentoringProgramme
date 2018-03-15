<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Change table collations from utf8 to utf8mb4
 */
class Version20161025164023 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE answers CONVERT TO CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE edition CONVERT TO CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE events CONVERT TO CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE persons CONVERT TO CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE users CONVERT TO CHARACTER SET utf8mb4');

        $this->addSql('ALTER TABLE rules CONVERT TO CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE rules_translations CONVERT TO CHARACTER SET utf8mb4');

        $this->addSql('ALTER TABLE about CONVERT TO CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE about_translations CONVERT TO CHARACTER SET utf8mb4');

        $this->addSql('ALTER TABLE questions CONVERT TO CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE questions_translations CONVERT TO CHARACTER SET utf8mb4');

        $this->addSql('ALTER TABLE images CONVERT TO CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE images_translations CONVERT TO CHARACTER SET utf8mb4');

        $this->addSql('ALTER TABLE choices CONVERT TO CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE choices_translations CONVERT TO CHARACTER SET utf8mb4');

        $this->addSql('ALTER TABLE mentors CONVERT TO CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE mentors_translations CONVERT TO CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE mentors_editions CONVERT TO CHARACTER SET utf8mb4');

        $this->addSql('ALTER TABLE partners CONVERT TO CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE partners_editions CONVERT TO CHARACTER SET utf8mb4');

        $this->addSql('ALTER TABLE sponsors CONVERT TO CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE sponsors_editions CONVERT TO CHARACTER SET utf8mb4');

        $this->addSql('ALTER TABLE migration_versions CONVERT TO CHARACTER SET utf8mb4');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE answers CONVERT TO CHARACTER SET utf8');
        $this->addSql('ALTER TABLE edition CONVERT TO CHARACTER SET utf8');
        $this->addSql('ALTER TABLE events CONVERT TO CHARACTER SET utf8');
        $this->addSql('ALTER TABLE persons CONVERT TO CHARACTER SET utf8');
        $this->addSql('ALTER TABLE users CONVERT TO CHARACTER SET utf8');

        $this->addSql('ALTER TABLE rules CONVERT TO CHARACTER SET utf8');
        $this->addSql('ALTER TABLE rules_translations CONVERT TO CHARACTER SET utf8');

        $this->addSql('ALTER TABLE about CONVERT TO CHARACTER SET utf8');
        $this->addSql('ALTER TABLE about_translations CONVERT TO CHARACTER SET utf8');

        $this->addSql('ALTER TABLE questions CONVERT TO CHARACTER SET utf8');
        $this->addSql('ALTER TABLE questions_translations CONVERT TO CHARACTER SET utf8');

        $this->addSql('ALTER TABLE images CONVERT TO CHARACTER SET utf8');
        $this->addSql('ALTER TABLE images_translations CONVERT TO CHARACTER SET utf8');

        $this->addSql('ALTER TABLE choices CONVERT TO CHARACTER SET utf8');
        $this->addSql('ALTER TABLE choices_translations CONVERT TO CHARACTER SET utf8');

        $this->addSql('ALTER TABLE mentors CONVERT TO CHARACTER SET utf8');
        $this->addSql('ALTER TABLE mentors_translations CONVERT TO CHARACTER SET utf8');
        $this->addSql('ALTER TABLE mentors_editions CONVERT TO CHARACTER SET utf8');

        $this->addSql('ALTER TABLE partners CONVERT TO CHARACTER SET utf8');
        $this->addSql('ALTER TABLE partners_editions CONVERT TO CHARACTER SET utf8');

        $this->addSql('ALTER TABLE sponsors CONVERT TO CHARACTER SET utf8');
        $this->addSql('ALTER TABLE sponsors_editions CONVERT TO CHARACTER SET utf8');

        $this->addSql('ALTER TABLE migration_versions CONVERT TO CHARACTER SET utf8');
    }
}
