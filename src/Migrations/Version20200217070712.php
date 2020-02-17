<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200217070712 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE blog_post (id INT NOT NULL, category_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_BA5AE01D12469DE2 ON blog_post (category_id)');
        $this->addSql('CREATE TABLE blog_category (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, posts_count INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE attachment (id SERIAL NOT NULL, file_name VARCHAR(255) NOT NULL, file_size INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE blog_post ADD CONSTRAINT FK_BA5AE01D12469DE2 FOREIGN KEY (category_id) REFERENCES blog_category (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE blog_post ADD CONSTRAINT FK_BA5AE01DBF396750 FOREIGN KEY (id) REFERENCES content (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE content ADD attachment_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE content ADD CONSTRAINT FK_FEC530A9464E68B FOREIGN KEY (attachment_id) REFERENCES attachment (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_FEC530A9464E68B ON content (attachment_id)');
        $this->addSql('ALTER TABLE formation DROP image');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE blog_post DROP CONSTRAINT FK_BA5AE01D12469DE2');
        $this->addSql('ALTER TABLE content DROP CONSTRAINT FK_FEC530A9464E68B');
        $this->addSql('DROP TABLE blog_post');
        $this->addSql('DROP TABLE blog_category');
        $this->addSql('DROP TABLE attachment');
        $this->addSql('ALTER TABLE formation ADD image VARCHAR(255) NOT NULL');
        $this->addSql('DROP INDEX IDX_FEC530A9464E68B');
        $this->addSql('ALTER TABLE content DROP attachment_id');
    }
}
