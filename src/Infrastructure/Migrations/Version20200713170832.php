<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200713170832 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout d\'un trigger pour automatiquement incrémenter le compteur de message sur le forum';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<SQL
CREATE FUNCTION countMessageForTopics() RETURNS TRIGGER AS
$$
BEGIN
    IF TG_OP IN ('DELETE') THEN
        UPDATE forum_topic SET message_count = message_count - 1 WHERE id = old.topic_id;
    END IF;
    IF TG_OP IN ('INSERT') THEN
        UPDATE forum_topic SET message_count = message_count + 1 WHERE id = new.topic_id;
    END IF;
    RETURN NULL;
END
$$
    LANGUAGE plpgsql;
SQL);

        $this->addSql(<<<SQL
CREATE TRIGGER countMessageForTopicsTrigger
    AFTER INSERT OR DELETE ON forum_message FOR EACH ROW
EXECUTE PROCEDURE countMessageForTopics();
SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(),
            'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TRIGGER countMessageForTopicsTrigger ON forum_message;');
        $this->addSql('DROP FUNCTION countMessageForTopics;');
    }
}
