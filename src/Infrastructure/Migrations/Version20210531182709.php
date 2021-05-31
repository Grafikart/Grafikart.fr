<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210531182709 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE podcast (id SERIAL NOT NULL, author_id INT NOT NULL, title VARCHAR(255) NOT NULL, content TEXT DEFAULT NULL, votes_count INT DEFAULT 0 NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, scheduled_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, confirmed_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, duration INT DEFAULT 0 NOT NULL, intervenants JSON DEFAULT \'[]\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D7E805BDF675F31B ON podcast (author_id)');
        $this->addSql('CREATE TABLE podcast_vote (id SERIAL NOT NULL, podcast_id INT NOT NULL, voter_id INT NOT NULL, weight DOUBLE PRECISION DEFAULT \'0\' NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B1341B28786136AB ON podcast_vote (podcast_id)');
        $this->addSql('CREATE INDEX IDX_B1341B28EBB4B8AD ON podcast_vote (voter_id)');
        $this->addSql('ALTER TABLE podcast ADD CONSTRAINT FK_D7E805BDF675F31B FOREIGN KEY (author_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE podcast_vote ADD CONSTRAINT FK_B1341B28786136AB FOREIGN KEY (podcast_id) REFERENCES podcast (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE podcast_vote ADD CONSTRAINT FK_B1341B28EBB4B8AD FOREIGN KEY (voter_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE podcast_vote DROP CONSTRAINT FK_B1341B28786136AB');
        $this->addSql('DROP TABLE podcast');
        $this->addSql('DROP TABLE podcast_vote');
    }
}
