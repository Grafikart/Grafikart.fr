<?php

declare(strict_types=1);

namespace App\Http\Admin\Data;

use App\Domain\Comment\Entity\Comment;

/**
 * @property Comment $entity
 */
class CommentCrudData extends AutomaticCrudData
{

    public string $content;
}
