<?php

namespace App\Domain\Comment;

use App\Domain\Application\Entity\Content;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    /**
     * Renvoie les commentaires associé à un contenu
     *
     * @return array<Comment>
     */
    public function findForContent(Content $content): array
    {
        $comments = $this->createQueryBuilder('c')
            ->where('c.target = :target')
            ->setParameter('target', $content)
            ->getQuery()
            ->getResult();
        return $comments;
    }

    /**
     * @param int $content
     * @return array<Comment>
     */
    public function findForApi(int $content): array
    {
        return $this->createQueryBuilder('c')
            ->select('partial c.{id, username, email, content, createdAt}, partial u.{id, username, email}, partial p.{id}')
            ->orderBy('c.createdAt', 'ASC')
            ->where('c.target = :content')
            // TODO : Repenser ça pour éviter les pbs de performances
            ->leftJoin('c.parent', 'p')
            ->leftJoin('c.author', 'u')
            ->setParameter('content', $content)
            ->getQuery()
            ->setHint(Query::HINT_FORCE_PARTIAL_LOAD, true)
            ->getResult();
    }

    /**
     * Renvoie un commentaire en évitant la liaison content
     *
     * @param int $id
     */
    public function findPartial(int $id): Comment
    {
        $result = $this->createQueryBuilder('c')
            ->select('partial c.{id, username, email, content, createdAt}, partial u.{id, username, email}')
            ->where('c.id = :id')
            ->leftJoin('c.author', 'u')
            ->setParameter('id', $id)
            ->setMaxResults(1)
            ->getQuery()
            ->setHint(Query::HINT_FORCE_PARTIAL_LOAD, true)
            ->getOneOrNullResult();
        if ($result === null) {
            throw new EntityNotFoundException();
        }
        return $result;
    }

}
