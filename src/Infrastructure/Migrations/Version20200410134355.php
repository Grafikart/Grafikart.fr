<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200410134355 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE forum_forum DROP CONSTRAINT fk_9d5f181a12469de2');
        $this->addSql('DROP SEQUENCE progress_id_seq1 CASCADE');
        $this->addSql('DROP SEQUENCE forum_category_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE forum_forum_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE forum_tag_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE forum_topic_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE forum_tag (id SERIAL PRIMARY KEY NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, position INT DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL)');
        $this->addSql('CREATE TABLE forum_topic (id SERIAL PRIMARY KEY NOT NULL, author_id INT NOT NULL, name VARCHAR(70) NOT NULL, content TEXT NOT NULL, solved BOOLEAN DEFAULT \'false\' NOT NULL, sticky BOOLEAN DEFAULT \'false\' NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL)');
        $this->addSql('CREATE INDEX IDX_853478CCF675F31B ON forum_topic (author_id)');
        $this->addSql('CREATE TABLE forum_topic_tag (topic_id INT NOT NULL, tag_id INT NOT NULL, PRIMARY KEY(topic_id, tag_id))');
        $this->addSql('CREATE INDEX IDX_302AC6211F55203D ON forum_topic_tag (topic_id)');
        $this->addSql('CREATE INDEX IDX_302AC621BAD26311 ON forum_topic_tag (tag_id)');
        $this->addSql('ALTER TABLE forum_topic ADD CONSTRAINT FK_853478CCF675F31B FOREIGN KEY (author_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE forum_topic_tag ADD CONSTRAINT FK_302AC6211F55203D FOREIGN KEY (topic_id) REFERENCES forum_topic (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE forum_topic_tag ADD CONSTRAINT FK_302AC621BAD26311 FOREIGN KEY (tag_id) REFERENCES forum_tag (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE forum_category');
        $this->addSql('DROP TABLE forum_forum');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE forum_topic_tag DROP CONSTRAINT FK_302AC621BAD26311');
        $this->addSql('ALTER TABLE forum_topic_tag DROP CONSTRAINT FK_302AC6211F55203D');
        $this->addSql('DROP SEQUENCE forum_tag_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE forum_topic_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE progress_id_seq1 INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE forum_category_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE forum_forum_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE forum_category (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, "position" SMALLINT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE forum_forum (id SERIAL NOT NULL, category_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, content VARCHAR(255) NOT NULL, "position" SMALLINT NOT NULL, topic_count INT NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, online BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_9d5f181a12469de2 ON forum_forum (category_id)');
        $this->addSql('ALTER TABLE forum_forum ADD CONSTRAINT fk_9d5f181a12469de2 FOREIGN KEY (category_id) REFERENCES forum_category (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE forum_tag');
        $this->addSql('DROP TABLE forum_topic');
        $this->addSql('DROP TABLE forum_topic_tag');
        $this->addSql('CREATE SEQUENCE progress_id_seq');
        $this->addSql('SELECT setval(\'progress_id_seq\', (SELECT MAX(id) FROM progress))');
    }
}
