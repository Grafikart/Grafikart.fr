<?php

declare(strict_types=1);

namespace App\Http\Admin\Data;

use App\Domain\Auth\User;
use App\Domain\Podcast\Entity\Podcast;
use Doctrine\Common\Collections\Collection;

/**
 * @property Podcast $entity
 */
class PodcastCrudData extends AutomaticCrudData
{
    public string $title = '';
    public Collection $intervenants;
    public ?\DateTimeInterface $scheduledAt = null;
    public ?string $youtube = '';
    public ?string $content = '';
    public ?User $author = null;
    public ?string $mp3 = null;
    public ?int $duration = null;
}
