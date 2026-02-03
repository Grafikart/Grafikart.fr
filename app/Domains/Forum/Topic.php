<?php

namespace App\Domains\Forum;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Topic extends Model
{
    protected $table = 'forum_topics';

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<TopicMessage, $this>
     */
    public function messages(): HasMany
    {
        return $this->hasMany(TopicMessage::class);
    }

    /**
     * @return BelongsToMany<TopicTag, $this>
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(TopicTag::class, 'forum_tag_topic', 'topic_id', 'tag_id');
    }
}
