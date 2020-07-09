<?php

namespace App\Tests\Domain\Blog;

use App\Domain\Blog\Post;
use PHPUnit\Framework\TestCase;

class PostTest extends TestCase
{
    public function videoContentDataProvider()
    {
        yield [false, ''];
        yield [true, "Une vidéo un peu différente que j'avais enregistré il y a un moment et que je n'avais pas publié à l'époque. Je pense que le sujet est toujours pertinent aujourd'hui.

https://www.youtube.com/watch?v=RtH0cH1p19g"];
        yield [false, "Une vidéo un peu [différente](https://www.youtube.com/watch?v=RtH0cH1p19g) que j'avais enregistré il y a un moment et que je n'avais pas publié à l'époque. Je pense que le sujet est toujours pertinent aujourd'hui."];
    }

    /**
     * @dataProvider videoContentDataProvider
     */
    public function testHasVideo(bool $expected, string $content)
    {
        $post = (new Post())->setContent($content);
        $this->assertEquals($expected, $post->hasVideo());
    }
}
