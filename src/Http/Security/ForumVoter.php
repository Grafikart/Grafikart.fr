<?php

namespace App\Http\Security;

use App\Domain\Auth\User;
use App\Domain\Forum\Entity\Message;
use App\Domain\Forum\Entity\Topic;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ForumVoter extends Voter
{

    const CREATE = "forumCreate";
    const REPORT = "forumReport";
    const CREATE_MESSAGE = "forumCreateMessage";
    const DELETE_MESSAGE = 'DELETE_FORUM_MESSAGE';

    protected function supports(string $attribute, $subject)
    {
        return in_array($attribute, [self::CREATE, self::REPORT, self::CREATE_MESSAGE, self::DELETE_MESSAGE]);
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        switch ($attribute) {
            case self::CREATE_MESSAGE:
                return $this->canCreateTopic($user, $subject);
            case self::DELETE_MESSAGE:
                return $this->ownMessage($user, $subject);
            case self::CREATE:
            case self::REPORT:
                return true;
        }

        return false;
    }

    protected function canCreateTopic(User $user, Topic $topic): bool
    {
        return !$topic->isSpam();
    }

    protected function ownMessage(User $user, Message $message): bool
    {
        return $message->getAuthor()->getId() === $user->getId();
    }
}
