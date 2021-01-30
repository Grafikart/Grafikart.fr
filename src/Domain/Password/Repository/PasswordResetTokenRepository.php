<?php

namespace App\Domain\Password\Repository;

use App\Domain\Password\Entity\PasswordResetToken;
use App\Infrastructure\Orm\AbstractRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<PasswordResetToken>
 */
class PasswordResetTokenRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PasswordResetToken::class);
    }
}
