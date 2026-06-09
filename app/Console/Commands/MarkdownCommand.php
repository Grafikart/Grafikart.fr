<?php

namespace App\Console\Commands;

use App\Domains\Blog\Post;
use App\Domains\Course\Course;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\File;

class MarkdownCommand extends Command
{
    protected $signature = 'app:markdown';

    protected $description = 'Export recent courses and blog posts to markdown files';

    public function handle(): int
    {
        $directory = public_path('uploads/markdown');
        File::ensureDirectoryExists($directory);

        $date = now()->subYears(4);
        $total = $this->export(Post::query()->published()->where('created_at', '>=', $date), $directory);
        $total += $this->export(Course::query()->published()->where('created_at', '>=', $date), $directory);

        $this->info("{$total} markdown files generated.");

        return self::SUCCESS;
    }

    /**
     * @template TModel of Post|Course
     *
     * @param  Builder<TModel>  $query
     */
    private function export(Builder $query, string $directory): int
    {
        $count = 0;

        foreach ($query->whereRaw('char_length(content) > ?', [1500])->latest()->cursor() as $item) {
            File::put($directory.'/'.$item->getAttribute('slug').'.md', $item->getAttribute('content'));
            $count++;
        }

        return $count;
    }
}
