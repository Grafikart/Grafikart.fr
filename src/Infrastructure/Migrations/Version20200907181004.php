<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200907181004 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cursus_modules (cursus_id INT NOT NULL, content_id INT NOT NULL, PRIMARY KEY(cursus_id, content_id))');
        $this->addSql('CREATE INDEX IDX_CFFEFB3440AEF4B9 ON cursus_modules (cursus_id)');
        $this->addSql('CREATE INDEX IDX_CFFEFB3484A0A3ED ON cursus_modules (content_id)');
        $this->addSql('ALTER TABLE cursus_modules ADD CONSTRAINT FK_CFFEFB3440AEF4B9 FOREIGN KEY (cursus_id) REFERENCES cursus (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE cursus_modules ADD CONSTRAINT FK_CFFEFB3484A0A3ED FOREIGN KEY (content_id) REFERENCES content (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE cursus_modules');
    }
}
