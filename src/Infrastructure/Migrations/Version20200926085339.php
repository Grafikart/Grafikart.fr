<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200926085339 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE badge (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, position INT DEFAULT 0 NOT NULL, action VARCHAR(255) NOT NULL, action_count INT DEFAULT 0 NOT NULL, theme VARCHAR(255) DEFAULT \'grey\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE badge_unlock (id SERIAL NOT NULL, owner_id INT NOT NULL, badge_id INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_585813AA7E3C61F9 ON badge_unlock (owner_id)');
        $this->addSql('CREATE INDEX IDX_585813AAF7A2C2FC ON badge_unlock (badge_id)');
        $this->addSql('ALTER TABLE badge_unlock ADD CONSTRAINT FK_585813AA7E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE badge_unlock ADD CONSTRAINT FK_585813AAF7A2C2FC FOREIGN KEY (badge_id) REFERENCES badge (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE badge_unlock DROP CONSTRAINT FK_585813AAF7A2C2FC');
        $this->addSql('DROP TABLE badge');
        $this->addSql('DROP TABLE badge_unlock');
    }
}
