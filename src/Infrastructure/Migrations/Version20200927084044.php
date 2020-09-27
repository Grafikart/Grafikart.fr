<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200927084044 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE badge ADD image VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE badge ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE badge_unlock DROP CONSTRAINT FK_585813AAF7A2C2FC');
        $this->addSql('ALTER TABLE badge_unlock ADD CONSTRAINT FK_585813AAF7A2C2FC FOREIGN KEY (badge_id) REFERENCES badge (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE badge_unlock DROP CONSTRAINT fk_585813aaf7a2c2fc');
        $this->addSql('ALTER TABLE badge_unlock ADD CONSTRAINT fk_585813aaf7a2c2fc FOREIGN KEY (badge_id) REFERENCES badge (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE badge DROP image');
        $this->addSql('ALTER TABLE badge DROP updated_at');
    }
}
