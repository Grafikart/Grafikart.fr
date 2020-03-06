<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200306125128 extends AbstractMigration
{
    public function getDescription() : string
    {
        return "Gestion de la suppression d'une formation avec un CASCADE SET NULL";
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE course DROP CONSTRAINT FK_169E6FB95200282E');
        $this->addSql('ALTER TABLE course ADD CONSTRAINT FK_169E6FB95200282E FOREIGN KEY (formation_id) REFERENCES formation (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE course DROP CONSTRAINT fk_169e6fb95200282e');
        $this->addSql('ALTER TABLE course ADD CONSTRAINT fk_169e6fb95200282e FOREIGN KEY (formation_id) REFERENCES formation (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
