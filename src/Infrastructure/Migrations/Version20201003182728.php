<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201003182728 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE transaction ADD firstname VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE transaction ADD lastname VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE transaction ADD address VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE transaction ADD city VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE transaction ADD postal_code VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE transaction ADD country_code VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE transaction DROP firstname');
        $this->addSql('ALTER TABLE transaction DROP lastname');
        $this->addSql('ALTER TABLE transaction DROP address');
        $this->addSql('ALTER TABLE transaction DROP city');
        $this->addSql('ALTER TABLE transaction DROP postal_code');
        $this->addSql('ALTER TABLE transaction DROP country_code');
    }
}
