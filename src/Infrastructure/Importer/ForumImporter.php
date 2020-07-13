<?php

namespace App\Infrastructure\Importer;

use App\Domain\Auth\User;
use App\Domain\Forum\Entity\Message;
use App\Domain\Forum\Entity\Tag;
use App\Domain\Forum\Entity\Topic;
use Cocur\Slugify\Slugify;
use Symfony\Component\Console\Style\SymfonyStyle;

class ForumImporter extends MySQLImporter
{
    public function import(SymfonyStyle $io): void
    {
        $io->title('Import des forum');
        $this->truncate('forum_tag');
        $this->truncate('forum_topic');
        $this->truncate('forum_topic_tag');
        $this->truncate('forum_message');

        // On importe les tags
        $query = $this->pdo->prepare(<<<SQL
            SELECT *
            FROM forum_forums
        SQL);
        $query->execute();
        /** @var array $rows */
        $rows = $query->fetchAll();
        $io->title('Importation des tags');
        $io->progressStart(count($rows));
        $this->disableAutoIncrement(Tag::class);
        foreach ($rows as $row) {
            $tag = (new Tag())
                ->setId($row['id'])
                ->setSlug((new Slugify())->slugify($row['name']))
                ->setName($row['name'])
                ->setPosition($row['order'])
                ->setCreatedAt(new \DateTime())
                ->setUpdatedAt(new \DateTime());
            $this->em->persist($tag);
            $io->progressAdvance();
        }
        $this->resetSequence('forum_tag', $row['id'] ?? 1);
        $io->progressFinish();
        $this->em->flush();
        $io->success(sprintf('Importation des %d tags', count($rows)));

        // On importe les topics
        $io->title('Importation des topics');
        $io->progressStart();
        $this->disableAutoIncrement(Topic::class);
        $offset = 0;
        while (true) {
            $query = $this->pdo->prepare(<<<SQL
                SELECT t.*
                FROM forum_topics t
                LEFT JOIN forum_forums f ON f.id = t.forum_id
                LEFT JOIN users u ON u.id = t.user_id
                WHERE f.id IS NOT NULL AND u.id IS NOT NULL
                ORDER BY t.id ASC
                LIMIT $offset, 1000
            SQL);
            $query->execute();
            /** @var array<string,mixed> $rows */
            $rows = $query->fetchAll();
            if (empty($rows)) {
                break;
            }
            foreach ($rows as $row) {
                /** @var Tag $tag */
                $tag = $this->em->getReference(Tag::class, $row['forum_id']);
                /** @var User $user */
                $user = $this->em->getReference(User::class, $row['user_id']);
                $topic = (new Topic())
                    ->setId($row['id'])
                    ->setName($row['name'])
                    ->setContent($row['content'])
                    ->setSolved($row['state'])
                    ->setSticky($row['sticky'])
                    ->setCreatedAt(new \DateTime($row['created_at']))
                    ->setUpdatedAt(new \DateTime($row['updated_at']))
                    ->setMessageCount($row['posts_count'])
                    ->addTag($tag)
                    ->setAuthor($user)
                ;
                $this->em->persist($topic);
                $io->progressAdvance();
            }
            $this->em->flush();
            $this->em->clear();
            $offset += 1000;
        }
        $this->resetSequence('forum_topic', $row['id'] ?? 1);
        $io->progressFinish();
        $io->success(sprintf('Importation des %d topics', $offset));

        // Import des messages
        $io->title('Importation des messages');
        $io->progressStart();
        $this->disableAutoIncrement(Message::class);
        $offset = 0;
        while (true) {
            $query = $this->pdo->prepare(<<<SQL
                SELECT p.*
                FROM forum_posts p
                LEFT JOIN users u ON u.id = p.user_id
                WHERE u.id IS NOT NULL
                ORDER BY p.id ASC
                LIMIT $offset, 1000
            SQL);
            $query->execute();
            /** @var array<string,mixed> $rows */
            $rows = $query->fetchAll();
            if (empty($rows)) {
                break;
            }
            foreach ($rows as $row) {
                $topic = $this->em->getRepository(Topic::class)->find($row['topic_id']);
                if ($topic) {
                    /** @var User $user */
                    $user = $this->em->getReference(User::class, $row['user_id']);
                    $message = (new Message())
                        ->setId($row['id'])
                        ->setTopic($topic)
                        ->setContent($row['content'])
                        ->setAccepted($row['solver'])
                        ->setCreatedAt(new \DateTime($row['created_at']))
                        ->setUpdatedAt(new \DateTime($row['updated_at']))
                        ->setAuthor($user)
                    ;
                    $topic->setLastMessage($message);
                    $this->em->persist($message);
                }
                $io->progressAdvance();
            }
            $this->em->flush();
            $this->em->clear();
            $offset += 1000;
        }
        $this->resetSequence('forum_message', $row['id'] ?? 1);
        $io->progressFinish();
        $io->success(sprintf('Importation des %d messages', $offset));
    }

    public function support(string $type): bool
    {
        return 'forum' === $type;
    }
}
