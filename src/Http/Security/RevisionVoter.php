<?php

namespace App\Http\Security;

use App\Domain\Application\Entity\Content;
use App\Domain\Auth\User;
use App\Domain\Comment\Comment;
use App\Domain\Revision\Revision;
use App\Http\Api\Resource\CommentResource;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class RevisionVoter extends Voter
{
    final public const ADD = 'add_revision';
    final public const DELETE = 'delete_revision';

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [
            self::ADD,
            self::DELETE,
        ]);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        // On ne peut ajouter de révision que sur les contenus en ligne
        if ($attribute === self::ADD &&
            $subject instanceof Content &&
            $subject->isOnline()
        ) {
            return true;
        }

        // Il faut être l'auteur d'une révision pour pouvoir la supprimer
        if ($attribute === self::DELETE &&
            $subject instanceof Revision &&
            $subject->getAuthor()->getId() === $user->getId()
        ) {
            return true;
        }

        return false;
    }
}
