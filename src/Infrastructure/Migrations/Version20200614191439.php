<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200614191439 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE technology_requirement (technology_source INT NOT NULL, technology_target INT NOT NULL, PRIMARY KEY(technology_source, technology_target))');
        $this->addSql('CREATE INDEX IDX_FA5618B015B5A9D8 ON technology_requirement (technology_source)');
        $this->addSql('CREATE INDEX IDX_FA5618B0C50F957 ON technology_requirement (technology_target)');
        $this->addSql('ALTER TABLE technology_requirement ADD CONSTRAINT FK_FA5618B015B5A9D8 FOREIGN KEY (technology_source) REFERENCES technology (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE technology_requirement ADD CONSTRAINT FK_FA5618B0C50F957 FOREIGN KEY (technology_target) REFERENCES technology (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE technology ADD type VARCHAR(50) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE technology_requirement');
        $this->addSql('ALTER TABLE technology DROP type');
    }
}
