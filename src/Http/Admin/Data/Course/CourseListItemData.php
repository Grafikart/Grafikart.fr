<?php

namespace App\Http\Admin\Data\Course;

use App\Component\ObjectMapper\Attribute\Map;
use App\Component\ObjectMapper\Transform\MapCollectionTransformer;
use App\Component\ObjectMapper\Transform\UrlTransformer;
use DateTimeImmutable;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
readonly class CourseListItemData
{

    public function __construct(
        public int    $id,
        public string $title,
        #[Map(source: 'id', transform: UrlTransformer::class)]
        public string $url,
        public DateTimeImmutable $createdAt,
        #[Map(source: 'online')]
        public bool $isOnline,
        #[Map(source: 'source')]
        public bool $hasSource,
        #[Map(source: 'mainTechnologies', transform: MapCollectionTransformer::class, context: ['targetClass' => TechnologyListItemData::class])]
        /** @var TechnologyListItemData[] */
        public array $technologies = [],
    )
    {

    }



}
