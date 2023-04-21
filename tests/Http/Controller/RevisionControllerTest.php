<?php

namespace App\Tests\Http\Controller;

use App\Domain\Auth\User;
use App\Domain\Blog\Post;
use App\Domain\Revision\Revision;
use App\Tests\FixturesTrait;
use App\Tests\WebTestCase;

class RevisionControllerTest extends WebTestCase
{
    use FixturesTrait;

    public function testAddRevisionWithoutAuth(): void
    {
        /** @var Post $post */
        /** @var User $user */
        [
            'post1' => $post,
        ] = $this->loadFixtures(['posts']);
        $this->client->request('GET', '/revision/' . $post->getId());
        $this->expectLoginRedirect();
    }

    public function testAddRevision(): void
    {
        /** @var Post $post */
        /** @var User $user */
        [
            'post1' => $post,
            'user1' => $user
        ] = $this->loadFixtures(['posts', 'users']);
        $this->login($user);
        $crawler = $this->client->request('GET', '/revision/' . $post->getId());
        $this->expectH1('Proposer un changement');
        $this->assertStringContainsString($post->getTitle(), $crawler->text());
    }

    public function testAddRevisionOnUnpublishedContent(): void
    {
        /** @var Post $post */
        /** @var User $user */
        [
            'post1' => $post,
            'user1' => $user
        ] = $this->loadFixtures(['posts', 'users']);
        $post->setOnline(false);
        $this->em->flush();
        $this->login($user);
        $this->client->request('GET', '/revision/' . $post->getId());
        $this->assertResponseStatusCodeSame(403);
    }

    public function testDeleteRevisionNotOwned(): void
    {
        /** @var Revision $revision1 */
        /** @var Revision $revision2 */
        [
            'revision1' => $revision1,
            'revision2' => $revision2
        ] = $this->loadFixtures(['revisions']);
        $this->login($revision1->getAuthor());
        $this->client->request('DELETE', '/revision/' . $revision2->getId());
        $this->assertResponseStatusCodeSame(403);
    }

    public function testDeleteRevisionOwned(): void
    {
        /** @var Revision $revision1 */
        [
            'revision1' => $revision1,
        ] = $this->loadFixtures(['revisions']);
        $this->login($revision1->getAuthor());
        $this->client->request('DELETE', '/revision/' . $revision1->getId());
        $this->assertEquals(1, $this->em->getRepository(Revision::class)->count([]));
        $this->assertResponseStatusCodeSame(204);
    }
}
