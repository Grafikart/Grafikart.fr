<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241007140600 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<SQL
            CREATE OR REPLACE FUNCTION upgrade_serial_to_identity(tbl regclass, col name)
            RETURNS void
            LANGUAGE plpgsql
            AS $$
            DECLARE
              colnum smallint;
              seqid oid;
              count int;
            BEGIN
              -- find column number
              SELECT attnum INTO colnum FROM pg_attribute WHERE attrelid = tbl AND attname = col;
              IF NOT FOUND THEN
                RAISE EXCEPTION 'column does not exist %', tbl;
              END IF;

              -- find sequence
                 SELECT INTO seqid objid
                FROM pg_depend
                WHERE (refclassid, refobjid, refobjsubid) = ('pg_class'::regclass, tbl, colnum)
                  AND classid = 'pg_class'::regclass AND objsubid = 0
                  AND deptype = 'a';

              GET DIAGNOSTICS count = ROW_COUNT;
              IF count < 1 THEN
                RETURN;
                RAISE EXCEPTION 'no linked sequence found %', tbl;
              ELSIF count > 1 THEN
                RAISE EXCEPTION 'more than one linked sequence found %', tbl;
              END IF;

              -- drop the default
              EXECUTE 'ALTER TABLE ' || tbl || ' ALTER COLUMN ' || quote_ident(col) || ' DROP DEFAULT';

              -- change the dependency between column and sequence to internal
              UPDATE pg_depend
                SET deptype = 'i'
                WHERE (classid, objid, objsubid) = ('pg_class'::regclass, seqid, 0)
                  AND deptype = 'a';

              -- mark the column as identity column
              UPDATE pg_attribute
                SET attidentity = 'd'
                WHERE attrelid = tbl
                  AND attname = col;
            END;
            $$;
        SQL);
        $this->addSql("SELECT upgrade_serial_to_identity('attachment', 'id')");
        $this->addSql("SELECT upgrade_serial_to_identity('badge', 'id')");
        $this->addSql("SELECT upgrade_serial_to_identity('badge_unlock', 'id')");
        $this->addSql("SELECT upgrade_serial_to_identity('blog_category', 'id')");
        $this->addSql("SELECT upgrade_serial_to_identity('comment', 'id')");
        $this->addSql("SELECT upgrade_serial_to_identity('contact_request', 'id')");
        $this->addSql("SELECT upgrade_serial_to_identity('content', 'id')");
        $this->addSql("SELECT upgrade_serial_to_identity('coupon', 'id')");
        $this->addSql("SELECT upgrade_serial_to_identity('cursus_category', 'id')");
        $this->addSql("SELECT upgrade_serial_to_identity('forum_message', 'id')");
        $this->addSql("SELECT upgrade_serial_to_identity('forum_read_time', 'id')");
        $this->addSql("SELECT upgrade_serial_to_identity('forum_report', 'id')");
        $this->addSql("SELECT upgrade_serial_to_identity('forum_tag', 'id')");
        $this->addSql("SELECT upgrade_serial_to_identity('forum_topic', 'id')");
        $this->addSql("SELECT upgrade_serial_to_identity('login_attempt', 'id')");
        $this->addSql("SELECT upgrade_serial_to_identity('messenger_messages', 'id')");
        $this->addSql("SELECT upgrade_serial_to_identity('notification', 'id')");
        $this->addSql("SELECT upgrade_serial_to_identity('password_reset_token', 'id')");
        $this->addSql("SELECT upgrade_serial_to_identity('plan', 'id')");
        $this->addSql("SELECT upgrade_serial_to_identity('podcast', 'id')");
        $this->addSql("SELECT upgrade_serial_to_identity('podcast_vote', 'id')");
        $this->addSql("SELECT upgrade_serial_to_identity('progress', 'id')");
        $this->addSql("SELECT upgrade_serial_to_identity('revision', 'id')");
        $this->addSql("SELECT upgrade_serial_to_identity('school', 'id')");
        $this->addSql("SELECT upgrade_serial_to_identity('subscription', 'id')");
        $this->addSql("SELECT upgrade_serial_to_identity('technology', 'id')");
        $this->addSql("SELECT upgrade_serial_to_identity('transaction', 'id')");
        $this->addSql("SELECT upgrade_serial_to_identity('user', 'id')");
        $this->addSql("SELECT upgrade_serial_to_identity('user_email_verification', 'id')");
        $this->addSql('DROP FUNCTION IF EXISTS upgrade_serial_to_identity;');
    }

    public function down(Schema $schema): void
    {
    }

    /*
    public function isTransactional(): bool
    {
        return false;
    }
    */
}
