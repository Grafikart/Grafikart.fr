<?php

namespace App\Domains\Blog;

use App\Domains\Attachment\Attachment;
use App\Domains\Blog\Factory\PostFactory;
use App\Helpers\MarkdownHelper;
use App\Infrastructure\Search\Contracts\Searchable;
use App\Infrastructure\Search\SearchDocument;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model implements Searchable
{
    protected $table = 'blog_posts';

    /** @use HasFactory<PostFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'online',
        'category_id',
        'attachment_id',
        'created_at',
    ];

    /**
     * @return BelongsTo<BlogCategory, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'category_id');
    }

    /**
     * @return BelongsTo<Attachment, $this>
     */
    public function attachment(): BelongsTo
    {
        return $this->belongsTo(Attachment::class);
    }

    protected function casts(): array
    {
        return [
            'online' => 'boolean',
            'created_at' => 'immutable_datetime',
            'updated_at' => 'immutable_datetime',
        ];
    }

    protected static function newFactory(): PostFactory
    {
        return PostFactory::new();
    }

    public function toSearchDocument(): ?SearchDocument
    {
        if (! $this->online) {
            return null;
        }

        return new SearchDocument(
            id: (string) $this->id,
            title: $this->title,
            content: MarkdownHelper::text($this->content),
            category: [],
            type: 'post',
            url: route('blog.show', $this),
            created_at: $this->created_at->getTimestamp(),
        );
    }
}
