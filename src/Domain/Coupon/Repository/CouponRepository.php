<?php

namespace App\Domain\Coupon\Repository;

use App\Domain\Coupon\Entity\Coupon;
use App\Domain\School\Entity\School;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\String\ByteString;

/**
 * @extends ServiceEntityRepository<Coupon>
 *
 * @method Coupon|null find($id, $lockMode = null, $lockVersion = null)
 * @method Coupon|null findOneBy(array $criteria, array $orderBy = null)
 * @method Coupon[]    findAll()
 * @method Coupon[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CouponRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Coupon::class);
    }

    public function save(Coupon $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Coupon $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function createForSchool(string $email, ?School $school = null, ?string $prefix = '', int $months = 1): Coupon
    {
        $prefix = empty($prefix) ? date('Y') : $prefix;
        $code = sprintf('%s_%s', $prefix, ByteString::fromRandom(8, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'));

        $coupon = (new Coupon())
            ->setSchool($school)
            ->setEmail($email)
            ->setMonths($months)
            ->setId($code);

        $this->save($coupon);

        return $coupon;
    }

    /**
     * @return Coupon[]
     */
    public function findAllUnclaimedForSchool(?School $school): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.school = :school')
            ->andWhere('c.claimedAt IS NULL')
            ->setParameter('school', $school)
            ->getQuery()
            ->getResult();
    }

}
