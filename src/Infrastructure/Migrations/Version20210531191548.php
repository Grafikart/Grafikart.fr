<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210531191548 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE podcast_user (podcast_id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY(podcast_id, user_id))');
        $this->addSql('CREATE INDEX IDX_66B74805786136AB ON podcast_user (podcast_id)');
        $this->addSql('CREATE INDEX IDX_66B74805A76ED395 ON podcast_user (user_id)');
        $this->addSql('ALTER TABLE podcast_user ADD CONSTRAINT FK_66B74805786136AB FOREIGN KEY (podcast_id) REFERENCES podcast (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE podcast_user ADD CONSTRAINT FK_66B74805A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE podcast DROP intervenants');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE podcast_user');
        $this->addSql('ALTER TABLE podcast ADD intervenants JSON DEFAULT \'[]\' NOT NULL');
    }
}
