<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200410161058 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE forum_message (id SERIAL NOT NULL, topic_id INT NOT NULL, author_id INT NOT NULL, accepted BOOLEAN DEFAULT \'false\' NOT NULL, content TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_47717D0E1F55203D ON forum_message (topic_id)');
        $this->addSql('CREATE INDEX IDX_47717D0EF675F31B ON forum_message (author_id)');
        $this->addSql('CREATE TABLE forum_tag (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, position INT DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE forum_topic (id SERIAL NOT NULL, author_id INT NOT NULL, last_message_id INT DEFAULT NULL, name VARCHAR(70) NOT NULL, content TEXT NOT NULL, solved BOOLEAN DEFAULT \'false\' NOT NULL, sticky BOOLEAN DEFAULT \'false\' NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, message_count INT DEFAULT 0 NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_853478CCF675F31B ON forum_topic (author_id)');
        $this->addSql('CREATE INDEX IDX_853478CCBA0E79C3 ON forum_topic (last_message_id)');
        $this->addSql('CREATE TABLE forum_topic_tag (topic_id INT NOT NULL, tag_id INT NOT NULL, PRIMARY KEY(topic_id, tag_id))');
        $this->addSql('CREATE INDEX IDX_E6342771F55203D ON forum_topic_tag (topic_id)');
        $this->addSql('CREATE INDEX IDX_E634277BAD26311 ON forum_topic_tag (tag_id)');
        $this->addSql('CREATE TABLE progress (id SERIAL NOT NULL, author_id INT NOT NULL, content_id INT NOT NULL, percent INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_2201F246F675F31B ON progress (author_id)');
        $this->addSql('CREATE INDEX IDX_2201F24684A0A3ED ON progress (content_id)');
        $this->addSql('CREATE TABLE revision (id SERIAL NOT NULL, author_id INT NOT NULL, target_id INT NOT NULL, content TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6D6315CCF675F31B ON revision (author_id)');
        $this->addSql('CREATE INDEX IDX_6D6315CC158E0B66 ON revision (target_id)');
        $this->addSql('CREATE TABLE blog_category (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, posts_count INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE blog_post (id INT NOT NULL, category_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_BA5AE01D12469DE2 ON blog_post (category_id)');
        $this->addSql('CREATE TABLE content (id SERIAL NOT NULL, attachment_id INT DEFAULT NULL, user_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, content TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, online BOOLEAN DEFAULT \'false\' NOT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_FEC530A9464E68B ON content (attachment_id)');
        $this->addSql('CREATE INDEX IDX_FEC530A9A76ED395 ON content (user_id)');
        $this->addSql('CREATE TABLE attachment (id SERIAL NOT NULL, file_name VARCHAR(255) NOT NULL, file_size INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE password_reset_token (id SERIAL NOT NULL, user_id INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, token VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6B7BA4B6A76ED395 ON password_reset_token (user_id)');
        $this->addSql('CREATE TABLE live (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, description TEXT NOT NULL, youtube_id VARCHAR(20) NOT NULL, duration INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE "user" (id SERIAL NOT NULL, username VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649F85E0677 ON "user" (username)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('CREATE TABLE login_attempt (id SERIAL NOT NULL, user_id INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8C11C1BA76ED395 ON login_attempt (user_id)');
        $this->addSql('CREATE TABLE comment (id SERIAL NOT NULL, author_id INT DEFAULT NULL, parent_id INT DEFAULT NULL, content_id INT NOT NULL, email VARCHAR(255) DEFAULT NULL, username VARCHAR(255) DEFAULT NULL, content TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_9474526CF675F31B ON comment (author_id)');
        $this->addSql('CREATE INDEX IDX_9474526C727ACA70 ON comment (parent_id)');
        $this->addSql('CREATE INDEX IDX_9474526C84A0A3ED ON comment (content_id)');
        $this->addSql('CREATE TABLE technology_usage (technology_id INT NOT NULL, content_id INT NOT NULL, version VARCHAR(15) DEFAULT NULL, secondary BOOLEAN DEFAULT \'false\' NOT NULL, PRIMARY KEY(technology_id, content_id))');
        $this->addSql('CREATE INDEX IDX_3098B4144235D463 ON technology_usage (technology_id)');
        $this->addSql('CREATE INDEX IDX_3098B41484A0A3ED ON technology_usage (content_id)');
        $this->addSql('CREATE TABLE technology (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, content TEXT DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE course (id INT NOT NULL, youtube_thumbnail_id INT DEFAULT NULL, deprecated_by_id INT DEFAULT NULL, formation_id INT DEFAULT NULL, duration SMALLINT DEFAULT 0 NOT NULL, youtube_id VARCHAR(12) DEFAULT NULL, video_path VARCHAR(255) DEFAULT NULL, source VARCHAR(255) DEFAULT NULL, demo VARCHAR(255) DEFAULT NULL, premium BOOLEAN DEFAULT \'false\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_169E6FB9C5BF4C20 ON course (youtube_thumbnail_id)');
        $this->addSql('CREATE INDEX IDX_169E6FB9DCF7B613 ON course (deprecated_by_id)');
        $this->addSql('CREATE INDEX IDX_169E6FB95200282E ON course (formation_id)');
        $this->addSql('CREATE TABLE formation (id INT NOT NULL, short TEXT DEFAULT NULL, chapters JSON NOT NULL, youtube_playlist VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE forum_message ADD CONSTRAINT FK_47717D0E1F55203D FOREIGN KEY (topic_id) REFERENCES forum_topic (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE forum_message ADD CONSTRAINT FK_47717D0EF675F31B FOREIGN KEY (author_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE forum_topic ADD CONSTRAINT FK_853478CCF675F31B FOREIGN KEY (author_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE forum_topic ADD CONSTRAINT FK_853478CCBA0E79C3 FOREIGN KEY (last_message_id) REFERENCES forum_message (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE forum_topic_tag ADD CONSTRAINT FK_E6342771F55203D FOREIGN KEY (topic_id) REFERENCES forum_topic (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE forum_topic_tag ADD CONSTRAINT FK_E634277BAD26311 FOREIGN KEY (tag_id) REFERENCES forum_tag (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE progress ADD CONSTRAINT FK_2201F246F675F31B FOREIGN KEY (author_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE progress ADD CONSTRAINT FK_2201F24684A0A3ED FOREIGN KEY (content_id) REFERENCES content (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE revision ADD CONSTRAINT FK_6D6315CCF675F31B FOREIGN KEY (author_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE revision ADD CONSTRAINT FK_6D6315CC158E0B66 FOREIGN KEY (target_id) REFERENCES content (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE blog_post ADD CONSTRAINT FK_BA5AE01D12469DE2 FOREIGN KEY (category_id) REFERENCES blog_category (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE blog_post ADD CONSTRAINT FK_BA5AE01DBF396750 FOREIGN KEY (id) REFERENCES content (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE content ADD CONSTRAINT FK_FEC530A9464E68B FOREIGN KEY (attachment_id) REFERENCES attachment (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE content ADD CONSTRAINT FK_FEC530A9A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE password_reset_token ADD CONSTRAINT FK_6B7BA4B6A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE login_attempt ADD CONSTRAINT FK_8C11C1BA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CF675F31B FOREIGN KEY (author_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C727ACA70 FOREIGN KEY (parent_id) REFERENCES comment (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C84A0A3ED FOREIGN KEY (content_id) REFERENCES content (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE technology_usage ADD CONSTRAINT FK_3098B4144235D463 FOREIGN KEY (technology_id) REFERENCES technology (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE technology_usage ADD CONSTRAINT FK_3098B41484A0A3ED FOREIGN KEY (content_id) REFERENCES content (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE course ADD CONSTRAINT FK_169E6FB9C5BF4C20 FOREIGN KEY (youtube_thumbnail_id) REFERENCES attachment (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE course ADD CONSTRAINT FK_169E6FB9DCF7B613 FOREIGN KEY (deprecated_by_id) REFERENCES course (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE course ADD CONSTRAINT FK_169E6FB95200282E FOREIGN KEY (formation_id) REFERENCES formation (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE course ADD CONSTRAINT FK_169E6FB9BF396750 FOREIGN KEY (id) REFERENCES content (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE formation ADD CONSTRAINT FK_404021BFBF396750 FOREIGN KEY (id) REFERENCES content (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE forum_topic DROP CONSTRAINT FK_853478CCBA0E79C3');
        $this->addSql('ALTER TABLE forum_topic_tag DROP CONSTRAINT FK_E634277BAD26311');
        $this->addSql('ALTER TABLE forum_message DROP CONSTRAINT FK_47717D0E1F55203D');
        $this->addSql('ALTER TABLE forum_topic_tag DROP CONSTRAINT FK_E6342771F55203D');
        $this->addSql('ALTER TABLE blog_post DROP CONSTRAINT FK_BA5AE01D12469DE2');
        $this->addSql('ALTER TABLE progress DROP CONSTRAINT FK_2201F24684A0A3ED');
        $this->addSql('ALTER TABLE revision DROP CONSTRAINT FK_6D6315CC158E0B66');
        $this->addSql('ALTER TABLE blog_post DROP CONSTRAINT FK_BA5AE01DBF396750');
        $this->addSql('ALTER TABLE comment DROP CONSTRAINT FK_9474526C84A0A3ED');
        $this->addSql('ALTER TABLE technology_usage DROP CONSTRAINT FK_3098B41484A0A3ED');
        $this->addSql('ALTER TABLE course DROP CONSTRAINT FK_169E6FB9BF396750');
        $this->addSql('ALTER TABLE formation DROP CONSTRAINT FK_404021BFBF396750');
        $this->addSql('ALTER TABLE content DROP CONSTRAINT FK_FEC530A9464E68B');
        $this->addSql('ALTER TABLE course DROP CONSTRAINT FK_169E6FB9C5BF4C20');
        $this->addSql('ALTER TABLE forum_message DROP CONSTRAINT FK_47717D0EF675F31B');
        $this->addSql('ALTER TABLE forum_topic DROP CONSTRAINT FK_853478CCF675F31B');
        $this->addSql('ALTER TABLE progress DROP CONSTRAINT FK_2201F246F675F31B');
        $this->addSql('ALTER TABLE revision DROP CONSTRAINT FK_6D6315CCF675F31B');
        $this->addSql('ALTER TABLE content DROP CONSTRAINT FK_FEC530A9A76ED395');
        $this->addSql('ALTER TABLE password_reset_token DROP CONSTRAINT FK_6B7BA4B6A76ED395');
        $this->addSql('ALTER TABLE login_attempt DROP CONSTRAINT FK_8C11C1BA76ED395');
        $this->addSql('ALTER TABLE comment DROP CONSTRAINT FK_9474526CF675F31B');
        $this->addSql('ALTER TABLE comment DROP CONSTRAINT FK_9474526C727ACA70');
        $this->addSql('ALTER TABLE technology_usage DROP CONSTRAINT FK_3098B4144235D463');
        $this->addSql('ALTER TABLE course DROP CONSTRAINT FK_169E6FB9DCF7B613');
        $this->addSql('ALTER TABLE course DROP CONSTRAINT FK_169E6FB95200282E');
        $this->addSql('DROP TABLE forum_message');
        $this->addSql('DROP TABLE forum_tag');
        $this->addSql('DROP TABLE forum_topic');
        $this->addSql('DROP TABLE forum_topic_tag');
        $this->addSql('DROP TABLE progress');
        $this->addSql('DROP TABLE revision');
        $this->addSql('DROP TABLE blog_category');
        $this->addSql('DROP TABLE blog_post');
        $this->addSql('DROP TABLE content');
        $this->addSql('DROP TABLE attachment');
        $this->addSql('DROP TABLE password_reset_token');
        $this->addSql('DROP TABLE live');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE login_attempt');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE technology_usage');
        $this->addSql('DROP TABLE technology');
        $this->addSql('DROP TABLE course');
        $this->addSql('DROP TABLE formation');
    }
}
