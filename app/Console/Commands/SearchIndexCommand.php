<?php

namespace App\Console\Commands;

use App\Domains\Blog\Post;
use App\Domains\Course\Course;
use App\Domains\Course\Formation;
use App\Infrastructure\Search\Contracts\IndexerInterface;
use App\Infrastructure\Search\Contracts\Searchable;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class SearchIndexCommand extends Command
{
    protected $signature = 'app:index';

    protected $description = 'Clean and rebuild the search index for all content';

    public function handle(IndexerInterface $indexer): int
    {
        $this->info('Cleaning index...');
        $indexer->clean();

        $this->indexModel($indexer, Post::class, Post::query()->where('online', true));
        $this->indexModel($indexer, Course::class, Course::query()->where('online', true));
        $this->indexModel($indexer, Formation::class, Formation::query()->where('online', true));

        $this->newLine();
        $this->info('Indexing complete!');

        return self::SUCCESS;
    }

    /**
     * @param  class-string  $model
     */
    private function indexModel(IndexerInterface $indexer, string $model, Builder $query): void
    {
        $this->newLine();
        $this->info("Indexing {$model}...");

        $count = $query->count();
        $bar = $this->output->createProgressBar($count);

        $query->chunkById(100, function ($items) use ($indexer, $bar) {
            foreach ($items as $item) {
                /** @var Searchable $item */
                $document = $item->toSearchDocument();
                if ($document !== null) {
                    $indexer->index($document->toArray());
                }
                $bar->advance();
            }
        });

        $bar->finish();
    }
}
