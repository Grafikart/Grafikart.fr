<?php

namespace App\Domain\Attachment\Repository;

use App\Domain\Application\Entity\Content;
use App\Domain\Attachment\Attachment;
use App\Domain\Course\Entity\Course;
use App\Infrastructure\Orm\AbstractRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<Attachment>
 */
class AttachmentRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Attachment::class);
    }

    public function findYearsMonths(): array
    {
        $rows = $this->createQueryBuilder('a')
            ->select('EXTRACT(MONTH FROM a.createdAt) as month, EXTRACT(YEAR FROM a.createdAt) as year, COUNT(a.id) as count')
            ->groupBy('month', 'year')
            ->orderBy('month', 'DESC')
            ->orderBy('year', 'DESC')
            ->getQuery()
            ->getResult();

        return array_map(fn(array $row) => [
            'path' => $row['year'] . '/' . str_pad((string)$row['month'], 2, '0', STR_PAD_LEFT),
            'count' => $row['count'],
        ], $rows);
    }

    /**
     * @return array<Attachment>
     */
    public function findForPath(string $path): array
    {
        $parts = explode('/', $path);
        $start = new \DateTimeImmutable("{$parts[0]}-{$parts[1]}-01");
        $end = $start->modify('+1 month -1 second');

        return $this->createQueryBuilder('a')
            ->where('a.createdAt BETWEEN :start AND :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->orderBy('a.createdAt', 'DESC')
            ->setMaxResults(50)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array<Attachment>
     */
    public function findLatest()
    {
        return $this->createQueryBuilder('a')
            ->orderBy('a.createdAt', 'DESC')
            ->setMaxResults(25)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array<Attachment>
     */
    public function search(string $q)
    {
        return $this->createQueryBuilder('a')
            ->where('a.fileName LIKE :search')
            ->orderBy('a.createdAt', 'DESC')
            ->setMaxResults(25)
            ->setParameter('search', "%$q%")
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les fichiers non rattachés à un contenu
     * @return array<Attachment>
     */
    public function orphaned(): array
    {
        return $this->createQueryBuilder('a')
            ->select('a')
            ->orderBy('a.createdAt', 'DESC')
            ->leftJoin(
                Content::class,
                'c',
                Join::WITH,
                'c.image = a.id'
            )
            ->leftJoin(
                Course::class,
                'course',
                Join::WITH,
                'course.youtubeThumbnail = a.id'
            )
            ->where('c.id IS NULL AND course.id IS NULL')
            ->setMaxResults(25)
            ->getQuery()
            ->getResult();
    }
}
