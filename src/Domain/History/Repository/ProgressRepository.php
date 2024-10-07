<?php

namespace App\Domain\History\Repository;

use App\Domain\Application\Entity\Content;
use App\Domain\Auth\User;
use App\Domain\Course\Entity\Course;
use App\Domain\Course\Entity\Formation;
use App\Domain\History\Entity\Progress;
use App\Infrastructure\Orm\AbstractRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<Progress>
 */
class ProgressRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Progress::class);
    }

    public function queryAllForUser(User $user): QueryBuilder
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.updatedAt', 'DESC')
            ->leftJoin('p.content', 'c')
            ->addSelect('c')
            ->where('(c INSTANCE OF ' . Course::class . ' OR c INSTANCE OF ' . Formation::class . ')')
            ->andWhere('p.author = :user')
            ->setParameter('user', $user->getId());
    }

    public function findOneByContent(User $user, Content $content): ?Progress
    {
        return $this->findOneBy([
            'content' => $content,
            'author' => $user,
        ]);
    }

    public function findLastForUser(User $user): array
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.content', 'c')
            ->addSelect('c')
            ->where('p.author = :user')
            ->andWhere('(c INSTANCE OF ' . Course::class . ' OR c INSTANCE OF ' . Formation::class . ')')
            ->andWhere('p.progress < :progress')
            ->orderBy('p.updatedAt', 'DESC')
            ->setMaxResults(4)
            ->setParameter('user', $user)
            ->setParameter('progress', Progress::TOTAL)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Content[] $contents
     *
     * @return array{id: int, progress: int}[]
     */
    public function findForContents(User $user, array $contents): array
    {
        return $this->createQueryBuilder('p')
            ->select('IDENTITY(p.content) as id', 'p.progress')
            ->where('p.content IN (:ids)')
            ->andWhere('p.author = :user')
            ->setParameter('ids', array_map(fn(Content $c) => $c->getId(), $contents))
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les ids lu parmis la liste passée en paramètre.
     *
     * @return int[]
     */
    public function findFinishedIdWithin(User $user, array $ids): array
    {
        return array_map(fn(Progress $p) => $p->getContent()->getId() ?: 0, $this->createQueryBuilder('p')
            ->where('p.content IN (:ids)')
            ->andWhere('p.author = :user')
            ->andWhere('p.progress = :total')
            ->setParameter('user', $user)
            ->setParameter('ids', $ids)
            ->setParameter('total', Progress::TOTAL)
            ->getQuery()
            ->getResult());
    }

    /**
     * Return the number of content completed by a user
     * @param int[] $users
     * @return array<string, int>
     */
    public function findCompletionForUsers(array $users): array
    {
        if (empty($users)) {
            return [];
        }
        $progressions = $this->createQueryBuilder('p')
            ->select('identity(p.author) AS user_id', 'COUNT(p.id) AS total')
            ->groupBy('user_id')
            ->where('identity(p.author) IN (:users)')
            ->setParameter('users', $users)
            ->getQuery()
            ->getArrayResult();
        return array_reduce($progressions, function (array $result, array $progress) {
            $result[$progress["user_id"]] = $progress["total"];
            return $result;
        }, []);
    }

    /**
     * Return the progress for formations as an associative array
     * @return array<int, int>
     */
    public function findSeenFormations(User $user): array
    {
        $results = $this->createQueryBuilder('p')
            ->leftJoin('p.content', 'c')
            ->select('identity(p.content) as content_id', 'p.progress as progress')
            ->where('c INSTANCE OF :type')
            ->andWhere('p.author = :user')
            ->orderBy('p.updatedAt', 'DESC')
            ->setParameter('type', 'formation')
            ->setParameter('user', $user)
            ->getQuery()
            ->getArrayResult();
        return array_reduce($results, function (array $acc, array $p) {
            $acc[$p["content_id"]] = $p["progress"];
            return $acc;
        }, []);
    }
}
