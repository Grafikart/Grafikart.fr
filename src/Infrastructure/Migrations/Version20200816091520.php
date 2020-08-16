<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200816091520 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Crée les champs nécessaire à la recherche sur le forum';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE forum_topic ADD search_vector tsvector DEFAULT NULL');
        $this->addSql('CREATE INDEX search_idx ON forum_topic USING GIN(search_vector)');
        $this->addSql('DROP TRIGGER IF EXISTS update_forum_document_trigger ON forum_topic;');
        $this->addSql('DROP FUNCTION IF EXISTS update_forum_document;');
        $this->addSql(<<<SQL
        CREATE FUNCTION update_forum_document() RETURNS trigger AS $$
        begin
          new.search_vector :=
          setweight(to_tsvector('french', coalesce(new.name, '')), 'A')
          || setweight(to_tsvector('french', coalesce(new.content, '')), 'B');
          return new;
        end
        $$ LANGUAGE plpgsql;
        SQL);
        $this->addSql('CREATE TRIGGER update_forum_document_trigger BEFORE INSERT OR UPDATE ON forum_topic FOR EACH ROW EXECUTE PROCEDURE update_forum_document();');
        $this->addSql("
        UPDATE forum_topic
        SET search_vector = setweight(to_tsvector('french', name), 'A') || setweight(to_tsvector('french', content), 'B')
        WHERE 1 = 1;");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE forum_topic DROP search_vector');
    }
}
