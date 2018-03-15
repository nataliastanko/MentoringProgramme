<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Create mentor faq
 */
class Version20171102020738 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE mentor_faq (id INT AUTO_INCREMENT NOT NULL, position INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mentor_faq_translations (id INT AUTO_INCREMENT NOT NULL, translatable_id INT DEFAULT NULL, question TEXT NOT NULL, answer TEXT NOT NULL, locale VARCHAR(255) NOT NULL, INDEX IDX_CB5F41232C2AC5D3 (translatable_id), UNIQUE INDEX mentor_faq_translations_unique_translation (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE mentor_faq_translations ADD CONSTRAINT FK_CB5F41232C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES mentor_faq (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE mentor_faq_translations DROP FOREIGN KEY FK_CB5F41232C2AC5D3');
        $this->addSql('DROP TABLE mentor_faq');
        $this->addSql('DROP TABLE mentor_faq_translations');
    }
}
