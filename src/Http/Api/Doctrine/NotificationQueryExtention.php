<?php

namespace App\Http\Api\Doctrine;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Domain\Auth\User;
use App\Domain\Notification\Entity\Notification;
use App\Domain\Notification\NotificationService;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Security;

/**
 * Filtre les notifications à renvoyer par l'API.
 */
final class NotificationQueryExtention implements QueryCollectionExtensionInterface
{
    private Security $security;
    private NotificationService $notificationService;

    public function __construct(Security $security, NotificationService $notificationService)
    {
        $this->security = $security;
        $this->notificationService = $notificationService;
    }

    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        string $operationName = null
    ): void {
        if (Notification::class !== $resourceClass) {
            return;
        }
        $user = $this->security->getUser();
        if ($user instanceof User) {
            $rootAlias = $queryBuilder->getRootAliases()[0];
            $queryBuilder->where(sprintf('%1$s.user = :user', $rootAlias))
                ->orWhere(sprintf('%1$s.user IS NULL AND %1$s.channel IN (:channels)', $rootAlias))
                ->andWhere(sprintf('%s.createdAt < NOW()', $rootAlias))
                ->orderBy(sprintf('%s.createdAt', $rootAlias), 'DESC')
                ->setMaxResults(5)
                ->setParameter('user', $user)
                ->setParameter('channels', $this->notificationService->getChannelsForUser($user));
        }
    }
}
