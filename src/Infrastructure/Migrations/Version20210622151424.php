<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210622151424 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE podcast ADD mp3 VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE podcast_vote DROP CONSTRAINT FK_B1341B28786136AB');
        $this->addSql('ALTER TABLE podcast_vote ADD CONSTRAINT FK_B1341B28786136AB FOREIGN KEY (podcast_id) REFERENCES podcast (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE podcast_vote DROP CONSTRAINT fk_b1341b28786136ab');
        $this->addSql('ALTER TABLE podcast_vote ADD CONSTRAINT fk_b1341b28786136ab FOREIGN KEY (podcast_id) REFERENCES podcast (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE podcast DROP mp3');
    }
}
