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
        $data = $this->loadFixtures(['comments']);
        $count1 = $this->em->getRepository(Comment::class)->count([]);
        $this->remove($data['user1']);
        $this->em->flush();
        $count2 = $this->em->getRepository(Comment::class)->count([]);
        $this->assertEquals($count1 - 1, $count2);
    }

    public function testCascadeDeleteForContent(): void
    {
        $data = $this->loadFixtures(['comments']);
        $count1 = $this->em->getRepository(Comment::class)->count([]);
        $this->remove($data['post1']);
        $this->em->flush();
        $count2 = $this->em->getRepository(Comment::class)->count([]);
        $this->assertEquals(0, $count2);
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
        $this->assertEquals(
            $user->getEmail(),
            $comment->getEmail()
        );
    }

}
