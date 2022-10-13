<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221013131432 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout des dépréciations pour les formations';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE formation ADD deprecated_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE formation ADD force_redirect BOOLEAN DEFAULT FALSE NOT NULL');
        $this->addSql('ALTER TABLE formation ADD CONSTRAINT FK_404021BFDCF7B613 FOREIGN KEY (deprecated_by_id) REFERENCES formation (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_404021BFDCF7B613 ON formation (deprecated_by_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE formation DROP CONSTRAINT FK_404021BFDCF7B613');
        $this->addSql('DROP INDEX IDX_404021BFDCF7B613');
        $this->addSql('ALTER TABLE formation DROP deprecated_by_id');
        $this->addSql('ALTER TABLE formation DROP force_redirect');
    }
}
