<?php

namespace App\Http\Security;

use App\Domain\Auth\User;
use App\Domain\Forum\Entity\Message;
use App\Domain\Forum\Entity\Topic;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ForumVoter extends Voter
{
    public final const CREATE = 'forumCreate';
    public final const REPORT = 'forumReport';
    public final const CREATE_MESSAGE = 'CREATE_FORUM_MESSAGE';
    public final const UPDATE_MESSAGE = 'UPDATE_FORUM_MESSAGE';
    public final const DELETE_MESSAGE = 'DELETE_FORUM_MESSAGE';
    public final const UPDATE_TOPIC = 'UPDATE_TOPIC';
    public final const DELETE_TOPIC = 'DELETE_TOPIC';
    public final const READ_TOPICS = 'READ_TOPICS';
    public final const SOLVE_MESSAGE = 'SOLVE_MESSAGE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [
            self::CREATE,
            self::REPORT,
            self::CREATE_MESSAGE,
            self::UPDATE_MESSAGE,
            self::DELETE_MESSAGE,
            self::UPDATE_TOPIC,
            self::DELETE_TOPIC,
            self::READ_TOPICS,
            self::SOLVE_MESSAGE,
        ]);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }
        return match ($attribute) {
            self::CREATE_MESSAGE => $this->canCreateMessageForTopic($user, $subject),
            self::UPDATE_TOPIC, self::DELETE_TOPIC => $this->canUpdateTopic($user, $subject),
            self::UPDATE_MESSAGE, self::DELETE_MESSAGE => $this->ownMessage($user, $subject),
            self::SOLVE_MESSAGE => $this->canSolve($user, $subject),
            self::READ_TOPICS, self::CREATE, self::REPORT => true,
            default => false,
        };
    }

    protected function canCreateMessageForTopic(User $user, Topic $topic): bool
    {
        return !$topic->isSpam() && !$topic->isLocked();
    }

    protected function ownMessage(User $user, Message $message): bool
    {
        return $message->getAuthor()->getId() === $user->getId();
    }

    private function canUpdateTopic(User $user, Topic $topic): bool
    {
        return $topic->getAuthor()->getId() === $user->getId();
    }

    private function canSolve(User $user, Message $message): bool
    {
        return $message->getTopic()->getAuthor()->getId() === $user->getId();
    }
}
