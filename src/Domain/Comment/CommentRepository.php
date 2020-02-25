<?php

namespace App\Domain\Comment;

use App\Domain\Application\Entity\Content;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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

}
