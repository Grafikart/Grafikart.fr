<?php

namespace App\Domain\Auth;

use App\Infrastructure\Orm\AbstractRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends AbstractRepository<User>
 */
class UserRepository extends AbstractRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findOneByEmail(string $email): ?User
    {
        return $this->findOneBy(['email' => $email]);
    }

    /**
     * Requête permettant de récupérer un utilisateur pour le login.
     */
    public function findForAuth(string $username): ?User
    {
        return $this->createQueryBuilder('u')
            ->where('LOWER(u.email) = :username')
            ->orWhere('LOWER(u.username) = :username')
            ->setMaxResults(1)
            ->setParameter('username', mb_strtolower($username))
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Cherche un utilisateur pour l'oauth.
     */
    public function findForOauth(string $service, ?string $serviceId, ?string $email): ?User
    {
        if (null === $serviceId || null === $email) {
            return null;
        }

        return $this->createQueryBuilder('u')
            ->where('u.email = :email')
            ->orWhere("u.{$service}Id = :serviceId")
            ->setMaxResults(1)
            ->setParameter('email', $email)
            ->setParameter('serviceId', $serviceId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    /**
     * @return User[]
     */
    public function clean(): array
    {
        $query = $this->createQueryBuilder('u')
            ->where('u.deleteAt IS NOT NULL')
            ->andWhere('u.deleteAt < NOW()');

        /** @var User[] $users */
        $users = $query->getQuery()->getResult();
        $query->delete(User::class, 'u')->getQuery()->execute();

        return $users;
    }

    /**
     * Renvoie la liste des ids discord des membres premiums.
     *
     * @return string[]
     */
    public function findPremiumDiscordIds(): array
    {
        return array_map(fn (array $user) => $user['discordId'], $this->createQueryBuilder('u')
            ->where('u.discordId IS NOT NULL AND u.discordId <> \'\'')
            ->andWhere('u.premiumEnd > NOW()')
            ->select('u.discordId')
            ->getQuery()
            ->getResult());
    }

    /**
     * Renvoie la liste des utilisateurs connectés depuis une certaine IP.
     *
     * @return User[]
     */
    public function findByIp(string $ip): array
    {
        return $this->createQueryBuilder('u')
            ->where('u.lastLoginIp = :ip')
            ->setMaxResults(15)
            ->setParameter('ip', $ip)
            ->getQuery()
            ->getResult();
    }

    /**
     * Liste les utilisateurs bannis.
     */
    public function queryBanned(): QueryBuilder
    {
        return $this->createQueryBuilder('u')
            ->where('u.bannedAt IS NOT NULL')
            ->orderBy('u.bannedAt', 'DESC');
    }
}
