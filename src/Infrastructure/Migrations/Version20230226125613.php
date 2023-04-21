<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Remove lives.
 */
final class Version20230226125613 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE live_id_seq CASCADE');
        $this->addSql('DROP TABLE live');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE live_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE live (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, description TEXT NOT NULL, youtube_id VARCHAR(20) NOT NULL, duration INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, image VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
    }
}
