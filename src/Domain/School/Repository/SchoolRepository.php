<?php

namespace App\Domain\School\Repository;

use App\Domain\Application\Entity\Content;
use App\Domain\Auth\User;
use App\Domain\School\Entity\School;
use App\Infrastructure\Orm\AbstractRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<School>
 */
class SchoolRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, School::class);
    }

    public function findStudentsForSchool(School $school): Query
    {
        $schoolOwnerId = $school->getOwner()?->getId();
        if (!$schoolOwnerId) {
            throw new \RuntimeException('School must have an owner');
        }
        return $this->getEntityManager()->createQuery(<<<DQL
            SELECT u FROM App\Domain\Auth\User u WHERE u.school = :school AND u.id != :owner
        DQL)->setParameter('school', $school)->setParameter('owner', $schoolOwnerId);
    }

    public function findAdministratedByUser(int $userId): ?School
    {
        return $this->createQueryBuilder('s')
            ->setMaxResults(1)
            ->where('s.owner = :user')
            ->setParameter('user', $userId)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
