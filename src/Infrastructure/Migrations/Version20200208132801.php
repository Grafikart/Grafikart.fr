<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200208132801 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE technology_usage (technology_id INT NOT NULL, content_id INT NOT NULL, version VARCHAR(15) DEFAULT NULL, secondary BOOLEAN DEFAULT FALSE NOT NULL, PRIMARY KEY(technology_id, content_id))');
        $this->addSql('CREATE INDEX IDX_3098B4144235D463 ON technology_usage (technology_id)');
        $this->addSql('CREATE INDEX IDX_3098B41484A0A3ED ON technology_usage (content_id)');
        $this->addSql('ALTER TABLE technology_usage ADD CONSTRAINT FK_3098B4144235D463 FOREIGN KEY (technology_id) REFERENCES technology (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE technology_usage ADD CONSTRAINT FK_3098B41484A0A3ED FOREIGN KEY (content_id) REFERENCES content (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE technology_usage');
    }
}
