<?php

namespace App\Tests\Http\Admin\Data\Course;

use App\Component\ObjectMapper\ObjectMapperInterface;
use App\Domain\Course\DTO\ContentTechnologyDTO;
use App\Domain\Course\Entity\Course;
use App\Domain\Course\Entity\Technology;
use App\Domain\Course\Entity\TechnologyUsage;
use App\Http\Admin\Data\Course\CourseFormInput;
use App\Tests\KernelTestCase;

class CourseFormInputTest extends KernelTestCase
{
    private ObjectMapperInterface $mapper;

    public function getInput(array $technologies = [])
    {
        return new CourseFormInput(
            createdAt: new \DateTimeImmutable(),
            title: 'My Course Title',
            content: 'My Course Content',
            level: 1,
            online: true,
            youtubeId: 'v123456',
            premium: true,
            forceRedirect: true,
            videoPath: '/path/to/video.mp4',
            demo: 'https://demo.example.com',
            deprecatedBy: 10,
            slug: 'my-course-slug',
            technologies: $technologies,
            image: 20,
            youtubeThumbnail: 30
        );
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->mapper = $this->getContainer()->get(ObjectMapperInterface::class);
    }

    public function testHydrateEntity(): void
    {
        $course = new Course();
        $input = $this->getInput(
            technologies: [new ContentTechnologyDTO(
                id: 1,
                version: 3,
                name: 'PHP',
                primary: true,
            ),
            ]);

        $this->mapper->map($input, $course);

        $this->assertEquals($input->title, $course->getTitle());
        $this->assertEquals($input->content, $course->getContent());
        $this->assertEquals($input->level, $course->getLevel());
        $this->assertEquals($input->online, $course->isOnline());
        $this->assertEquals($input->premium, $course->getPremium());
        $this->assertEquals($input->forceRedirect, $course->isForceRedirect());
        $this->assertEquals($input->videoPath, $course->getVideoPath());
        $this->assertEquals($input->demo, $course->getDemo());
        $this->assertEquals($input->createdAt, $course->getCreatedAt());
        $this->assertEquals($input->youtubeId, $course->getYoutubeId());
        $this->assertSame($input->deprecatedBy, $course->getDeprecatedBy()->getId());
        $this->assertSame($input->image, $course->getImage()->getId());
        $this->assertSame($input->youtubeThumbnail, $course->getYoutubeThumbnail()->getId());
        $this->assertCount(1, $course->getTechnologyUsages());
    }

    public function testSyncRemovesOldTechnologies()
    {
        $course = new Course();
        $t1 = new TechnologyUsage()->setTechnology($this->em->getReference(Technology::class, 1))->setContent($course);
        $t2 = new TechnologyUsage()->setTechnology($this->em->getReference(Technology::class, 2))->setContent($course);
        $course->addTechnologyUsage($t1);
        $course->addTechnologyUsage($t2);

        $input = $this->getInput(
            technologies: [
                new ContentTechnologyDTO(
                    id: 2,
                    version: 3,
                ),
                new ContentTechnologyDTO(
                    id: 3,
                ),
            ]
        );
        $this->mapper->map($input, $course);
        $this->assertCount(2, $course->getTechnologyUsages());
        $this->assertEquals(2, $course->getTechnologyUsages()->first()->getTechnology()->getId());
        $this->assertEquals(3, $course->getTechnologyUsages()->first()->getVersion()


    }
}
