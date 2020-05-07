<?php

namespace App\Http\Api\Doctrine;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Domain\Auth\User;
use App\Domain\Notification\Entity\Notification;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Security;

/**
 * Filtre les notifications à renvoyer par l'API
 */
final class NotificationQueryExtention implements QueryCollectionExtensionInterface
{

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        string $operationName = null
    ): void {
        if ($resourceClass !== Notification::class) {
            return;
        }
        $user = $this->security->getUser();
        if ($user instanceof User) {
            $rootAlias = $queryBuilder->getRootAliases()[0];
            $queryBuilder->where(sprintf('%1$s.user = :user OR %1$s.user IS NULL', $rootAlias))
                ->andWhere(sprintf('%s.createdAt < NOW()', $rootAlias))
                ->orderBy(sprintf('%s.createdAt', $rootAlias), 'DESC')
                ->setMaxResults(5)
                ->setParameter('user', $user);
        }
    }

}
