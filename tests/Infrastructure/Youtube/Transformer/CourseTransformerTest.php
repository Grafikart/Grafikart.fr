<?php

namespace App\Tests\Infrastructure\Youtube\Transformer;

use App\Domain\Course\Entity\Course;
use App\Domain\Course\Entity\Formation;
use App\Domain\Course\Entity\Technology;
use App\Domain\Course\Entity\TechnologyUsage;
use App\Infrastructure\Youtube\Transformer\CourseTransformer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\SerializerInterface;

class CourseTransformerTest extends TestCase
{

    private CourseTransformer $transformer;

    public function setUp(): void
    {
        $serializer = $this->getMockBuilder(SerializerInterface::class)->disableOriginalConstructor()->getMock();
        $serializer->expects($this->any())->method('serialize')->willReturn('https://grafikart.fr/tutoriel');
        $this->transformer = new CourseTransformer($serializer);
        parent::setUp();
    }

    public function testVideoObject(): void
    {
        $usage = (new TechnologyUsage())->setTechnology((new Technology())->setName('PHP'));
        $course = (new Course())->setTitle('Formulaire de contact')->addTechnologyUsage($usage);
        $this->assertSame('Tutoriel PHP : Formulaire de contact', $this->transformer->transform($course)->getSnippet()->getTitle());
        $course->setTitle('Découverte de la balise <h1>');
        $this->assertSame('Tutoriel PHP : Découverte de la balise h1', $this->transformer->transform($course)->getSnippet()->getTitle());
        $formation = (new Formation())->setTitle('Formation de test');
        $course->setFormation($formation);
        $this->assertSame('Formation de test : Découverte de la balise h1', $this->transformer->transform($course)->getSnippet()->getTitle());
    }

    public function testVisibility(): void
    {
        $usage = (new TechnologyUsage())->setTechnology((new Technology())->setName('PHP'));
        $course = (new Course())->setTitle('Formulaire de contact')->addTechnologyUsage($usage)->setCreatedAt(new \DateTimeImmutable('-10 days'));
        $this->assertSame('public', $this->transformer->transform($course)->getStatus()->getPrivacyStatus());
        $course->setCreatedAt(new \DateTimeImmutable('+ 10 days'));
        $this->assertSame('private', $this->transformer->transform($course)->getStatus()->getPrivacyStatus());
    }
}
