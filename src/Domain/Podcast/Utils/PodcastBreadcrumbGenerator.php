<?php

namespace App\Domain\Podcast\Utils;

use App\Domain\Podcast\Entity\Podcast;
use App\Normalizer\Breadcrumb\BreadcrumbGeneratorInterface;
use App\Normalizer\Breadcrumb\BreadcrumbItem;

class PodcastBreadcrumbGenerator implements BreadcrumbGeneratorInterface
{
    /**
     * @param Podcast $podcast
     */
    public function generate($podcast): array
    {
        $items = [];
        $items[] = new BreadcrumbItem('Podcasts', ['podcast']);
        $items[] = new BreadcrumbItem((string) $podcast->getTitle(), ['podcast_show', [
            'id' => $podcast->getId(),
        ]]);

        return $items;
    }

    public function support(object $object): bool
    {
        return $object instanceof Podcast;
    }
}
