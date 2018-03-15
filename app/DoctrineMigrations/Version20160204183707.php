<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160204183707 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE about (id INT AUTO_INCREMENT NOT NULL, position INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE about_translations (id INT AUTO_INCREMENT NOT NULL, translatable_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, content LONGTEXT DEFAULT NULL, locale VARCHAR(255) NOT NULL, INDEX IDX_2A703A5D2C2AC5D3 (translatable_id), UNIQUE INDEX about_translations_unique_translation (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE answers (id INT AUTO_INCREMENT NOT NULL, question_id INT NOT NULL, person_id INT NOT NULL, choice_id INT DEFAULT NULL, content LONGTEXT DEFAULT NULL, INDEX IDX_50D0C6061E27F6BF (question_id), INDEX IDX_50D0C606217BBB47 (person_id), INDEX IDX_50D0C606998666D1 (choice_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE choices (id INT AUTO_INCREMENT NOT NULL, question_id INT DEFAULT NULL, INDEX IDX_5CE96391E27F6BF (question_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE choices_translations (id INT AUTO_INCREMENT NOT NULL, translatable_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, locale VARCHAR(255) NOT NULL, INDEX IDX_8A14B4272C2AC5D3 (translatable_id), INDEX name_idx (name), UNIQUE INDEX choices_translations_unique_translation (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE persons (id INT AUTO_INCREMENT NOT NULL, mentor_id INT DEFAULT NULL, name VARCHAR(100) NOT NULL, last_name VARCHAR(100) DEFAULT NULL, email VARCHAR(100) NOT NULL, age INT NOT NULL, education VARCHAR(100) NOT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME NOT NULL, INDEX IDX_A25CC7D3DB403044 (mentor_id), INDEX search_idx (name, last_name, email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE questions (id INT AUTO_INCREMENT NOT NULL, position INT NOT NULL, deletedAt DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE questions_translations (id INT AUTO_INCREMENT NOT NULL, translatable_id INT DEFAULT NULL, name LONGTEXT NOT NULL, helpblock VARCHAR(255) DEFAULT NULL, locale VARCHAR(255) NOT NULL, INDEX IDX_E33C9B9C2C2AC5D3 (translatable_id), UNIQUE INDEX questions_translations_unique_translation (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mentors (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, last_name VARCHAR(100) DEFAULT NULL, email VARCHAR(100) DEFAULT NULL, position INT NOT NULL, photoName VARCHAR(255) DEFAULT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mentors_translations (id INT AUTO_INCREMENT NOT NULL, translatable_id INT DEFAULT NULL, bio LONGTEXT NOT NULL, locale VARCHAR(255) NOT NULL, INDEX IDX_363BA06C2C2AC5D3 (translatable_id), UNIQUE INDEX mentors_translations_unique_translation (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE partners (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, email VARCHAR(100) DEFAULT NULL, position INT NOT NULL, photoName VARCHAR(255) DEFAULT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE partners_translations (id INT AUTO_INCREMENT NOT NULL, translatable_id INT DEFAULT NULL, description LONGTEXT NOT NULL, locale VARCHAR(255) NOT NULL, INDEX IDX_37856C9A2C2AC5D3 (translatable_id), UNIQUE INDEX partners_translations_unique_translation (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, username_canonical VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, email_canonical VARCHAR(255) NOT NULL, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, locked TINYINT(1) NOT NULL, expired TINYINT(1) NOT NULL, expires_at DATETIME DEFAULT NULL, confirmation_token VARCHAR(255) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', credentials_expired TINYINT(1) NOT NULL, credentials_expire_at DATETIME DEFAULT NULL, name VARCHAR(100) DEFAULT NULL, last_name VARCHAR(100) DEFAULT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME NOT NULL, UNIQUE INDEX UNIQ_1483A5E992FC23A8 (username_canonical), UNIQUE INDEX UNIQ_1483A5E9A0D96FBF (email_canonical), INDEX search_idx (name, last_name, email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rules (id INT AUTO_INCREMENT NOT NULL, position INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rules_translations (id INT AUTO_INCREMENT NOT NULL, translatable_id INT DEFAULT NULL, content LONGTEXT NOT NULL, locale VARCHAR(255) NOT NULL, INDEX IDX_D1F1DF6C2C2AC5D3 (translatable_id), UNIQUE INDEX rules_translations_unique_translation (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sponsors (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, email VARCHAR(100) DEFAULT NULL, description LONGTEXT NOT NULL, position INT NOT NULL, photoName VARCHAR(255) DEFAULT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE about_translations ADD CONSTRAINT FK_2A703A5D2C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES about (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE answers ADD CONSTRAINT FK_50D0C6061E27F6BF FOREIGN KEY (question_id) REFERENCES questions (id)');
        $this->addSql('ALTER TABLE answers ADD CONSTRAINT FK_50D0C606217BBB47 FOREIGN KEY (person_id) REFERENCES persons (id)');
        $this->addSql('ALTER TABLE answers ADD CONSTRAINT FK_50D0C606998666D1 FOREIGN KEY (choice_id) REFERENCES choices (id)');
        $this->addSql('ALTER TABLE choices ADD CONSTRAINT FK_5CE96391E27F6BF FOREIGN KEY (question_id) REFERENCES questions (id)');
        $this->addSql('ALTER TABLE choices_translations ADD CONSTRAINT FK_8A14B4272C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES choices (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE persons ADD CONSTRAINT FK_A25CC7D3DB403044 FOREIGN KEY (mentor_id) REFERENCES mentors (id)');
        $this->addSql('ALTER TABLE questions_translations ADD CONSTRAINT FK_E33C9B9C2C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES questions (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE mentors_translations ADD CONSTRAINT FK_363BA06C2C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES mentors (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE partners_translations ADD CONSTRAINT FK_37856C9A2C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES partners (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE rules_translations ADD CONSTRAINT FK_D1F1DF6C2C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES rules (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE about_translations DROP FOREIGN KEY FK_2A703A5D2C2AC5D3');
        $this->addSql('ALTER TABLE answers DROP FOREIGN KEY FK_50D0C606998666D1');
        $this->addSql('ALTER TABLE choices_translations DROP FOREIGN KEY FK_8A14B4272C2AC5D3');
        $this->addSql('ALTER TABLE answers DROP FOREIGN KEY FK_50D0C606217BBB47');
        $this->addSql('ALTER TABLE answers DROP FOREIGN KEY FK_50D0C6061E27F6BF');
        $this->addSql('ALTER TABLE choices DROP FOREIGN KEY FK_5CE96391E27F6BF');
        $this->addSql('ALTER TABLE questions_translations DROP FOREIGN KEY FK_E33C9B9C2C2AC5D3');
        $this->addSql('ALTER TABLE persons DROP FOREIGN KEY FK_A25CC7D3DB403044');
        $this->addSql('ALTER TABLE mentors_translations DROP FOREIGN KEY FK_363BA06C2C2AC5D3');
        $this->addSql('ALTER TABLE partners_translations DROP FOREIGN KEY FK_37856C9A2C2AC5D3');
        $this->addSql('ALTER TABLE rules_translations DROP FOREIGN KEY FK_D1F1DF6C2C2AC5D3');
        $this->addSql('DROP TABLE about');
        $this->addSql('DROP TABLE about_translations');
        $this->addSql('DROP TABLE answers');
        $this->addSql('DROP TABLE choices');
        $this->addSql('DROP TABLE choices_translations');
        $this->addSql('DROP TABLE persons');
        $this->addSql('DROP TABLE questions');
        $this->addSql('DROP TABLE questions_translations');
        $this->addSql('DROP TABLE mentors');
        $this->addSql('DROP TABLE mentors_translations');
        $this->addSql('DROP TABLE partners');
        $this->addSql('DROP TABLE partners_translations');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE rules');
        $this->addSql('DROP TABLE rules_translations');
        $this->addSql('DROP TABLE sponsors');
    }
}
