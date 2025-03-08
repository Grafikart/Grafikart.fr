<?php

namespace App\Http\Security;

use App\Domain\Blog\Post;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class PostVoter extends Voter
{
    final public const SHOW = 'show';

    public function __construct(private RequestStack $requestStack)
    {
    }

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [
            self::SHOW,
        ]) && ($subject instanceof Post);
    }

    /**
     * @param string $attribute
     * @param Post   $subject
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        if (!($subject instanceof Post)) {
            return false;
        }
        if ($this->requestStack->getMainRequest()?->query->getBoolean('preview')) {
            return true;
        }

        return $subject->isOnline() && $subject->getCreatedAt() < new \DateTimeImmutable();
    }
}
