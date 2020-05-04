<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200504162512 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE forum_report (id SERIAL NOT NULL, author_id INT NOT NULL, topic_id INT DEFAULT NULL, message_id INT DEFAULT NULL, reason VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_DC804455F675F31B ON forum_report (author_id)');
        $this->addSql('CREATE INDEX IDX_DC8044551F55203D ON forum_report (topic_id)');
        $this->addSql('CREATE INDEX IDX_DC804455537A1329 ON forum_report (message_id)');
        $this->addSql('ALTER TABLE forum_report ADD CONSTRAINT FK_DC804455F675F31B FOREIGN KEY (author_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE forum_report ADD CONSTRAINT FK_DC8044551F55203D FOREIGN KEY (topic_id) REFERENCES forum_topic (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE forum_report ADD CONSTRAINT FK_DC804455537A1329 FOREIGN KEY (message_id) REFERENCES forum_message (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE forum_report');
    }
}
