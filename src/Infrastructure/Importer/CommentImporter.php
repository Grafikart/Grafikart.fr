<?php

namespace App\Infrastructure\Importer;

use App\Domain\Auth\User;
use App\Domain\Blog\Post;
use App\Domain\Comment\Comment;
use App\Domain\Course\Entity\Course;
use Symfony\Component\Console\Style\SymfonyStyle;

final class CommentImporter extends MySQLImporter
{

    public function import(SymfonyStyle $io): void
    {
        $this->truncate('comment');
        $offset = 0;
        $io->title('Importation des utilisateurs');
        $query = $this->pdo->prepare("SELECT COUNT(id) as count FROM comments");
        $query->execute();
        $result = $query->fetch();
        $io->progressStart($result['count']);
        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);
        $this->disableAutoIncrement(Comment::class);
        $parents = [];
        $lastId = 0;
        while (true) {
            $query = $this->pdo->prepare("
                SELECT
                    CASE
                        WHEN c.commentable_type = 'Tutoriel' THEN t.slug
                        WHEN c.commentable_type = 'Post' THEN p.slug
                        ELSE ''
                    END as slug,
                       c.*
                FROM comments c
                LEFT JOIN tutoriels t ON c.commentable_id = t.id AND c.commentable_type = 'Tutoriel'
                LEFT JOIN posts p ON c.commentable_id = p.id AND c.commentable_type = 'Post'
                ORDER BY id ASC
                LIMIT $offset, 1000
            ");
            $query->execute();
            /** @var array<string,mixed> $oldComments */
            $oldComments = $query->fetchAll();
            if (empty($oldComments)) {
                break;
            }
            foreach ($oldComments as $row) {
                $comment = (new Comment())
                    ->setId($row['id'])
                    ->setUsername($row['username'])
                    ->setEmail($row['email'])
                    ->setContent($row['content'])
                    ->setCreatedAt(new \DateTime($row['created_at']));
                if ($row['parent_id'] === null) {
                    $parents[$row['id']] = $row['id'];
                }
                if ($row['parent_id'] && isset($parents[$row['parent_id']])) {
                    $comment->setParent($this->em->getReference(Comment::class, $row['parent_id']));
                }
                if ($row['user_id']) {
                    $user = $this->em->getRepository(User::class)->find($row['user_id']);
                    if ($user === null) {
                        $comment->setUsername('John Doe')->setEmail('john@doe.fr');
                    } else {
                        $comment->setAuthor($this->em->getReference(User::class, $row['user_id']));
                    }
                }
                $valid = $this->attachContent($comment, $row['commentable_type'], $row['commentable_id'], $row['slug']);
                if ($valid) {
                    $this->em->persist($comment);
                }
                $io->progressAdvance();
                $lastId = $row['id'];
            }
            $this->em->flush();
            $this->em->clear();
            $offset += 1000;
        }
        $lastId++;
        $this->em->getConnection()->exec("ALTER SEQUENCE comment_id_seq RESTART WITH $lastId;");
        $io->progressFinish();
        $io->success(sprintf('Importation de %d commentaires', $result['count']));
    }

    private function attachContent(Comment $comment, string $type, int $id, ?string $slug): bool
    {
        if ($type === 'Tutoriel' && $id > 0) {
            /** @var Course $course */
            $course = $this->em->getReference(Course::class, $id);
            $comment->setTarget($course);
            return true;
        }
        if ($type === 'Post' && $id > 0 && $slug) {
            $post = $this->em->getRepository(Post::class)->findOneBy(['slug' => $slug]);
            if ($post) {
                $comment->setTarget($post);
                return true;
            }
        }
        return false;
    }

}
