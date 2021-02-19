<?php

namespace App\Tests\Domain\Comment;

use App\Domain\Auth\User;
use App\Domain\Comment\Comment;
use App\Tests\FixturesTrait;
use App\Tests\KernelTestCase;

class CommentTest extends KernelTestCase
{
    use FixturesTrait;

    public function testCascadeDeleteForUser(): void
    {
        /** @var Comment $comment */
        ['comment_user' => $comment] = $this->loadFixtures(['comments']);
        $commentRepository = $this->em->getRepository(Comment::class);
        $commentId = $comment->getId();
        $this->remove($comment->getAuthor());
        $this->em->flush();
        $this->em->clear();
        $this->assertEquals(null, $commentRepository->find($commentId));
    }

    public function testCascadeDeleteForContent(): void
    {
        $data = $this->loadFixtures(['comments']);
        $count = $this->em->getRepository(Comment::class)->count([]);
        $this->remove($data['post1']);
        $this->em->flush();
        $count2 = $this->em->getRepository(Comment::class)->count([]);
        $this->assertLessThan($count, $count2);
    }

    public function testGetUserNameWithAttachedUser(): void
    {
        $user = new User();
        $user->setUsername('John');
        $user->setEmail('john@doe.fr');
        $comment = new Comment();
        $comment->setAuthor($user);
        $this->assertEquals(
            $user->getUsername(),
            $comment->getUsername()
        );
    }
}
