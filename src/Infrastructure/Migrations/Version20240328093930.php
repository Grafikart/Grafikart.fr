<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240328093930 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE school_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE school (id INT NOT NULL, owner_id INT NOT NULL, name VARCHAR(255) NOT NULL, email_template TEXT DEFAULT NULL, credits INT DEFAULT 0 NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F99EDABB7E3C61F9 ON school (owner_id)');
        $this->addSql('ALTER TABLE school ADD CONSTRAINT FK_F99EDABB7E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "user" ADD school_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT FK_8D93D649C32A47EE FOREIGN KEY (school_id) REFERENCES school (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_8D93D649C32A47EE ON "user" (school_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT FK_8D93D649C32A47EE');
        $this->addSql('DROP SEQUENCE school_id_seq CASCADE');
        $this->addSql('ALTER TABLE school DROP CONSTRAINT FK_F99EDABB7E3C61F9');
        $this->addSql('DROP TABLE school');
        $this->addSql('DROP INDEX IDX_8D93D649C32A47EE');
        $this->addSql('ALTER TABLE "user" DROP school_id');
    }
}
