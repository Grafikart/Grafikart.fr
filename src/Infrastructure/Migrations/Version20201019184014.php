<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201019184014 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cursus_category (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, description TEXT NOT NULL, position INT DEFAULT 0 NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE cursus ADD category_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE cursus ADD CONSTRAINT FK_255A0C312469DE2 FOREIGN KEY (category_id) REFERENCES cursus_category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_255A0C312469DE2 ON cursus (category_id)');
        $this->addSql('ALTER TABLE "user" ALTER premium_end TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE "user" ALTER premium_end DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN "user".premium_end IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cursus DROP CONSTRAINT FK_255A0C312469DE2');
        $this->addSql('DROP TABLE cursus_category');
        $this->addSql('ALTER TABLE "user" ALTER premium_end TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE "user" ALTER premium_end DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN "user".premium_end IS NULL');
        $this->addSql('DROP INDEX IDX_255A0C312469DE2');
        $this->addSql('ALTER TABLE cursus DROP category_id');
    }
}
