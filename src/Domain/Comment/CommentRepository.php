<?php

namespace App\Domain\Comment;

use App\Domain\Auth\User;
use App\Domain\Comment\Entity\Comment;
use App\Infrastructure\Orm\AbstractRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<Comment>
 */
class CommentRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    /**
     * Récupère les commentaires pour le listing de l'API en évitant la liaison content.
     *
     * @return array<Comment>
     */
    public function findForApi(int $content): array
    {
        // Force l'enregistrement de l'entité dans l'entity manager pour éviter les requêtes supplémentaires
        $this->getEntityManager()->getReference(\App\Domain\Blog\Post::class, $content);

        return $this->createQueryBuilder('c')
            ->select('c, u')
            ->orderBy('c.createdAt', 'ASC')
            ->where('c.target = :content')
            ->leftJoin('c.author', 'u')
            ->setParameter('content', $content)
            ->getQuery()
            ->getResult();
    }

    public function queryLatest(): Query
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.createdAt', 'DESC')
            ->join('c.target', 't')
            ->leftJoin('c.author', 'a')
            ->addSelect('t', 'a')
            ->setMaxResults(7)
            ->getQuery();
    }

    public function findLastByUser(User $user): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.author = :user')
            ->orderBy('c.createdAt', 'DESC')
            ->setMaxResults(4)
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les commentaires suspicieux qui sont potentiellement des spams.
     *
     * @param string[] $words
     */
    public function querySuspicious(array $words): QueryBuilder
    {
        $query = $this->createQueryBuilder('row')
            ->where('row.content LIKE :search')
            ->orderBy('row.createdAt', 'DESC')
            ->setParameter('search', '%http%');
        foreach ($words as $k => $word) {
            $query = $query->orWhere("row.content LIKE :spam{$k}")->setParameter("spam{$k}", "%{$word}%");
        }

        return $query;
    }

    public function queryByIp(string $ip): QueryBuilder
    {
        return $this->createQueryBuilder('row')
            ->where('row.ip LIKE :ip')
            ->setParameter('ip', $ip);
    }
}
