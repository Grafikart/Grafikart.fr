<?php

namespace App\Infrastructure\Importer;

use App\Domain\Attachment\Attachment;
use App\Domain\Auth\User;
use App\Domain\Blog\Category;
use App\Domain\Blog\Post;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\KernelInterface;

class BlogImporter
{

    use DatabaseImporterTools;

    private \PDO $pdo;
    private EntityManagerInterface $em;
    private KernelInterface $kernel;

    public function __construct(\PDO $pdo, EntityManagerInterface $em, KernelInterface $kernel)
    {
        $this->pdo = $pdo;
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
        $this->em = $em;
        $this->kernel = $kernel;
    }

    public function import(SymfonyStyle $io): void
    {
        $this->importCategory($io);
        $this->importPosts($io);
    }

    public function importCategory(SymfonyStyle $io): void
    {
        $this->truncate('blog_category');
        $query = $this->pdo->prepare(<<<SQL
            SELECT c.id, c.name, c.slug
            FROM categories as c
            JOIN posts p on c.id = p.category_id
            GROUP BY c.id
        SQL);
        $query->execute();
        /** @var array<mixed> $rows */
        $rows = $query->fetchAll();
        $io->title('Importation des catégories');
        $io->progressStart(count($rows));
        foreach ($rows as $row) {
            $category = (new Category())
                ->setName($row['name'])
                ->setSlug($row['slug'])
                ->setId($row['id']);
            $this->disableAutoIncrement($category);
            $this->em->persist($category);
            $io->progressAdvance();
        }
        $io->progressFinish();
        $io->success(sprintf('Importation de %d categories', count($rows)));
        $this->em->flush();
    }

    public function importPosts(SymfonyStyle $io): void
    {
        $this->truncate($this->em->getClassMetadata(Post::class)->getTableName());
        $this->truncate($this->em->getClassMetadata(Attachment::class)->getTableName());
        $query = $this->pdo->prepare(<<<SQL
            SELECT p.name, p.slug, p.content, p.created_at, p.user_id, p.category_id, p.online, p.image
            FROM posts as p
        SQL);
        $query->execute();
        /** @var array<mixed> $rows */
        $rows = $query->fetchAll();
        $io->title('Importation des articles');
        $io->progressStart(count($rows));
        foreach ($rows as $row) {
            $filePath  = $this->kernel->getProjectDir() . '/public/old/posts/1/' . $row['image'];
            $attachment = null;
            if (file_exists($filePath)) {
                $file = new ImportedFile($this->kernel->getProjectDir() . '/public/old/posts/1/' . $row['image']);
                $attachment = (new Attachment())
                    ->setFile($file)
                    ->setCreatedAt(new \DateTime($row['created_at']));
            }
            /** @var User $user */
            $user = $this->em->getReference(User::class, $row['user_id']);
            /** @var Category $category */
            $category = $this->em->getReference(Category::class, $row['category_id']);
            $entity = (new Post())
                ->setCategory($category)
                ->setSlug($row['slug'])
                ->setCreatedAt(new \DateTime($row['created_at']))
                ->setUpdatedAt(new \DateTime($row['created_at']))
                ->setContent($row['content'])
                ->setTitle($row['name'])
                ->setOnline($row['online'])
                ->setImage($attachment)
                ->setAuthor($user);
            if (!in_array((int)$row['user_id'], [49939])) {
                $this->em->persist($entity);
            }
            $io->progressAdvance();
        }
        $this->em->flush();
        $io->progressFinish();
        $io->success(sprintf('Importation de %d articles', count($rows)));
    }
}
