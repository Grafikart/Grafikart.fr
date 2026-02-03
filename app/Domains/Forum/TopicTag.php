<?php

namespace App\Domains\Forum;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TopicTag extends Model
{
    protected $table = 'forum_tags';

    public $timestamps = false;

    /**
     * @return BelongsTo<TopicTag, $this>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(TopicTag::class, 'parent_id');
    }

    /**
     * @return HasMany<TopicTag, $this>
     */
    public function children(): HasMany
    {
        return $this->hasMany(TopicTag::class, 'parent_id');
    }

    /**
     * @return BelongsToMany<Topic, $this>
     */
    public function topics(): BelongsToMany
    {
        return $this->belongsToMany(Topic::class, 'forum_tag_topic');
    }
}
