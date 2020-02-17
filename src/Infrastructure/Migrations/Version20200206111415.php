<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200206111415 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Création des tutoriels & formations';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE content (id SERIAL PRIMARY KEY NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, content TEXT NOT NULL, type VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE TABLE course (id INT PRIMARY KEY NOT NULL, deprecated_by_id INT DEFAULT NULL, formation_id INT DEFAULT NULL, duration SMALLINT DEFAULT 0 NOT NULL, youtube_id VARCHAR(12) DEFAULT NULL, video_path VARCHAR(255) DEFAULT NULL, source BOOLEAN DEFAULT FALSE NOT NULL, demo VARCHAR(255) DEFAULT NULL, premium BOOLEAN DEFAULT FALSE NOT NULL)');
        $this->addSql('CREATE TABLE formation (id INT PRIMARY KEY NOT NULL, chapters JSON NOT NULL, duration INT NOT NULL, youtube_playlist VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE INDEX IDX_169E6FB9DCF7B613 ON course (deprecated_by_id)');
        $this->addSql('CREATE INDEX IDX_169E6FB95200282E ON course (formation_id)');
        $this->addSql('ALTER TABLE course ADD CONSTRAINT FK_169E6FB9DCF7B613 FOREIGN KEY (deprecated_by_id) REFERENCES course (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE course ADD CONSTRAINT FK_169E6FB95200282E FOREIGN KEY (formation_id) REFERENCES formation (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE course ADD CONSTRAINT FK_169E6FB9BF396750 FOREIGN KEY (id) REFERENCES content (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE formation ADD CONSTRAINT FK_404021BFBF396750 FOREIGN KEY (id) REFERENCES content (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE course DROP CONSTRAINT FK_169E6FB9BF396750');
        $this->addSql('ALTER TABLE formation DROP CONSTRAINT FK_404021BFBF396750');
        $this->addSql('ALTER TABLE course DROP CONSTRAINT FK_169E6FB9DCF7B613');
        $this->addSql('ALTER TABLE course DROP CONSTRAINT FK_169E6FB95200282E');
        $this->addSql('DROP TABLE content');
        $this->addSql('DROP TABLE course');
        $this->addSql('DROP TABLE formation');
    }
}
