<?php

namespace App\Http\Security;

use App\Domain\Auth\User;
use App\Domain\Comment\Entity\Comment;
use App\Http\Api\Resource\CommentResource;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class SchoolVoter extends Voter
{

    final public const MANAGE = 'SCHOOL_MANAGE';
    final public const IMPORT = 'SCHOOL_IMPORT';

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [
                self::MANAGE,
                self::IMPORT,
            ]);
    }

    /**
     * @param string                  $attribute
     * @param Comment|CommentResource $subject
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        if ($attribute === self::MANAGE) {
            return $user->isSchoolOwner();
        }

        if ($attribute === self::IMPORT) {
            return $user->isSchoolOwner() && $user->getSchool()->getCredits() > 0;
        }

        return false;
    }

}
