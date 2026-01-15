<?php

namespace App\Domains\Blog;

use App\Domains\Blog\Factory\BlogCategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogCategory extends Model
{
    /** @use HasFactory<BlogCategoryFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
    ];

    protected static function newFactory(): BlogCategoryFactory
    {
        return BlogCategoryFactory::new();
    }
}
