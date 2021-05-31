<?php

declare(strict_types=1);

namespace App\Http\Admin\Data;

use App\Domain\Auth\User;
use App\Domain\Podcast\Entity\Podcast;

/**
 * @property Podcast $entity
 */
class PodcastCrudData extends AutomaticCrudData
{

    public string $title = '';
    public ?User $author = null;
    public ?\DateTimeInterface $confirmedAt = null;
    public ?\DateTimeInterface $scheduledAt = null;
    public string $content = '';
}
