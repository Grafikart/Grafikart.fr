<?php

namespace App\Http\Admin\Data\Course;

use App\Domain\Course\Entity\Course;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
readonly class CourseData
{
    public string $url;

    public int $id;
    public string $title;
    public string $slug;
    public \DateTimeInterface $createdAt;
    public bool $online;
    public bool $premium;
    public bool $forceRedirect;
    public string $videoPath;
    public string $demo;
    public string $youtubeId;
    public int $duration;
    public ?int $deprecatedBy;
    public string $content;
    public int $level;
    /** @var TechnologyData[] */
    public array $technologies;

    public function __construct(
        Course $course,
    ) {
        $this->id = (int) $course->getId();
        $this->title = $course->getTitle() ?? '';
        $this->slug = $course->getSlug() ?? '';
        $this->createdAt = $course->getCreatedAt();
        $this->online = $course->isOnline();
        $this->premium = $course->getPremium();
        $this->forceRedirect = $course->isForceRedirect();
        $this->videoPath = $course->getVideoPath() ?? '';
        $this->demo = $course->getDemo() ?? '';
        $this->youtubeId = $course->getYoutubeId() ?? '';
        $this->duration = $course->getDuration();
        $this->deprecatedBy = $course->getDeprecatedBy()?->getId();
        $this->content = $course->getContent() ?? '';
        $this->level = $course->getLevel();
        $this->technologies = $course->getTechnologyUsages()
            ->map(TechnologyData::fromUsage(...))
            ->toArray();
        $this->url = sprintf('/tutoriels/%s-%s', $this->slug, $this->id);
    }
}
