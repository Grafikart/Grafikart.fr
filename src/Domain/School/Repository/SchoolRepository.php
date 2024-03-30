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

    /**
     * @return User[]
     */
    public function findStudentsForSchool(School $school): Query
    {
        return $this->getEntityManager()->createQuery(<<<DQL
            SELECT u FROM App\Domain\Auth\User u WHERE u.school = :school AND u.id != :owner
        DQL)->setParameter('school', $school)->setParameter('owner', $school->getOwner()->getId());
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
