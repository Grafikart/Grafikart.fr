<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200410153705 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE forum_topic ADD message_count INT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER INDEX idx_302ac6211f55203d RENAME TO IDX_E6342771F55203D');
        $this->addSql('ALTER INDEX idx_302ac621bad26311 RENAME TO IDX_E634277BAD26311');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE forum_topic DROP message_count');
        $this->addSql('ALTER INDEX idx_e634277bad26311 RENAME TO idx_302ac621bad26311');
        $this->addSql('ALTER INDEX idx_e6342771f55203d RENAME TO idx_302ac6211f55203d');
    }
}
