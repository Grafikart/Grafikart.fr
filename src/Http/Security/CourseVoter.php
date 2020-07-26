<?php

namespace App\Http\Security;

use App\Domain\Auth\User;
use App\Domain\Course\Entity\Course;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class CourseVoter extends Voter
{
    const DOWNLOAD_SOURCE = 'DOWNLOAD_SOURCE';
    const DOWNLOAD_VIDEO = 'DOWNLOAD_VIDEO';

    /**
     * {@inheritdoc}
     */
    protected function supports(string $attribute, $subject)
    {
        return in_array($attribute, [
                self::DOWNLOAD_SOURCE,
                self::DOWNLOAD_VIDEO,
            ]) && $subject instanceof Course;
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        return $user instanceof User && $user->isPremium();
    }
}
