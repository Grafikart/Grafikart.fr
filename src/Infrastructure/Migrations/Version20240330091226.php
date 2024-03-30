<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240330091226 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE coupon (id VARCHAR(255) NOT NULL, school_id INT DEFAULT NULL, claimed_by_id INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, claimed_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, email VARCHAR(255) NOT NULL, months INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_64BF3F02C32A47EE ON coupon (school_id)');
        $this->addSql('CREATE INDEX IDX_64BF3F02F67E7A38 ON coupon (claimed_by_id)');
        $this->addSql('COMMENT ON COLUMN coupon.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN coupon.claimed_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE school (id SERIAL NOT NULL, owner_id INT NOT NULL, name VARCHAR(255) NOT NULL, email_message TEXT DEFAULT NULL, email_subject TEXT DEFAULT NULL, coupon_prefix TEXT DEFAULT NULL, credits INT DEFAULT 0 NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F99EDABB7E3C61F9 ON school (owner_id)');
        $this->addSql('CREATE TABLE school_user (school_id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY(school_id, user_id))');
        $this->addSql('CREATE INDEX IDX_CCBB09A4C32A47EE ON school_user (school_id)');
        $this->addSql('CREATE INDEX IDX_CCBB09A4A76ED395 ON school_user (user_id)');
        $this->addSql('ALTER TABLE coupon ADD CONSTRAINT FK_64BF3F02C32A47EE FOREIGN KEY (school_id) REFERENCES school (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE coupon ADD CONSTRAINT FK_64BF3F02F67E7A38 FOREIGN KEY (claimed_by_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE school ADD CONSTRAINT FK_F99EDABB7E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE school_user ADD CONSTRAINT FK_CCBB09A4C32A47EE FOREIGN KEY (school_id) REFERENCES school (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE school_user ADD CONSTRAINT FK_CCBB09A4A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP INDEX idx_8d93d649c32a47ee');
        $this->addSql('ALTER TABLE "user" DROP school_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE coupon DROP CONSTRAINT FK_64BF3F02C32A47EE');
        $this->addSql('ALTER TABLE coupon DROP CONSTRAINT FK_64BF3F02F67E7A38');
        $this->addSql('ALTER TABLE school DROP CONSTRAINT FK_F99EDABB7E3C61F9');
        $this->addSql('ALTER TABLE school_user DROP CONSTRAINT FK_CCBB09A4C32A47EE');
        $this->addSql('ALTER TABLE school_user DROP CONSTRAINT FK_CCBB09A4A76ED395');
        $this->addSql('DROP TABLE coupon');
        $this->addSql('DROP TABLE school');
        $this->addSql('DROP TABLE school_user');
        $this->addSql('ALTER TABLE "user" ADD school_id INT DEFAULT NULL');
        $this->addSql('CREATE INDEX idx_8d93d649c32a47ee ON "user" (school_id)');
    }
}
