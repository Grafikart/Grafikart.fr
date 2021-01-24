<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210122163451 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE forum_topic DROP CONSTRAINT FK_853478CCBA0E79C3');
        $this->addSql('ALTER TABLE forum_topic ADD CONSTRAINT FK_853478CCBA0E79C3 FOREIGN KEY (last_message_id) REFERENCES forum_message (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE forum_topic DROP CONSTRAINT fk_853478ccba0e79c3');
        $this->addSql('ALTER TABLE forum_topic ADD CONSTRAINT fk_853478ccba0e79c3 FOREIGN KEY (last_message_id) REFERENCES forum_message (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
