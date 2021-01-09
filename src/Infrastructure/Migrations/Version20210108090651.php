<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210108090651 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE forum_tag ADD visible BOOLEAN DEFAULT \'true\' NOT NULL');
        $this->addSql('ALTER TABLE "user" ALTER country SET DEFAULT \'FR\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE forum_tag DROP visible');
        $this->addSql('ALTER TABLE "user" ALTER country DROP DEFAULT');
    }
}
