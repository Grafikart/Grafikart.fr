<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200401164006 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE forum_category (id SERIAL PRIMARY KEY, name VARCHAR(255) NOT NULL, "order" SMALLINT NOT NULL)');
        $this->addSql('CREATE TABLE forum_forum (id SERIAL PRIMARY KEY, category_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, content VARCHAR(255) NOT NULL, "order" SMALLINT NOT NULL, topic_count INT NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, online BOOLEAN NOT NULL)');
        $this->addSql('CREATE INDEX IDX_9D5F181A12469DE2 ON forum_forum (category_id)');
        $this->addSql('ALTER TABLE forum_forum ADD CONSTRAINT FK_9D5F181A12469DE2 FOREIGN KEY (category_id) REFERENCES forum_category (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE forum_forum DROP CONSTRAINT FK_9D5F181A12469DE2');
        $this->addSql('DROP TABLE forum_category');
        $this->addSql('DROP TABLE forum_forum');
    }
}
