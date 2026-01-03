<?php

namespace App\Http\Admin\Data\Course;

use App\Domain\Course\DTO\ContentTechnologyDTO;
use App\Domain\Course\Entity\Course;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

#[TypeScript]
readonly class CourseFormData
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
    /** @var ContentTechnologyDTO[] */
    public array $technologies;
    public string $image;
    public string $youtubeThumbnail;

    public function __construct(
        Course $course,
        UploaderHelper $uploaderHelper,
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
            ->map(ContentTechnologyDTO::fromUsage(...))
            ->toArray();
        $this->url = sprintf('/tutoriels/%s-%s', $this->slug, $this->id);
        $this->image = $uploaderHelper->asset($course->getImage()) ?? '';
        $this->youtubeThumbnail = $uploaderHelper->asset($course->getYoutubeThumbnail()) ?? '';
    }
}
