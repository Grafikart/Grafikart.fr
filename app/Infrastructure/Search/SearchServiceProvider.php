<?php

namespace App\Infrastructure\Search;

use App\Infrastructure\Search\Contracts\IndexerInterface;
use App\Infrastructure\Search\Contracts\SearchInterface;
use App\Infrastructure\Search\Meilisearch\MeilisearchClient;
use App\Infrastructure\Search\Meilisearch\MeilisearchIndexer;
use App\Infrastructure\Search\Meilisearch\MeilisearchSearch;
use App\Infrastructure\Search\Typesense\TypesenseClient;
use App\Infrastructure\Search\Typesense\TypesenseIndexer;
use App\Infrastructure\Search\Typesense\TypesenseSearch;
use Illuminate\Http\Client\Factory;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class SearchServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $driver = config('search.default');
        if (empty($driver)) {
            return;
        }
        Event::subscribe(IndexerSubscriber::class);
    }

    public function register(): void
    {
        $driver = config('search.default');
        if (empty($driver)) {
            return;
        }
        $config = config("search.engines.{$driver}");

        match ($driver) {
            'typesense' => $this->registerTypesense($config),
            'meilisearch' => $this->registerMeilisearch($config),
            default => throw new \InvalidArgumentException("Unsupported search driver: {$driver}"),
        };
    }

    /**
     * @param  array{endpoint: string, key: string}  $config
     */
    private function registerTypesense(array $config): void
    {
        $this->app->singleton(TypesenseClient::class, function () use ($config) {
            return new TypesenseClient(
                $config['endpoint'],
                $config['key'],
                app(Factory::class),
            );
        });

        $this->app->bind(IndexerInterface::class, TypesenseIndexer::class);
        $this->app->bind(SearchInterface::class, TypesenseSearch::class);
    }

    /**
     * @param  array{endpoint: string, key: string}  $config
     */
    private function registerMeilisearch(array $config): void
    {
        $this->app->singleton(MeilisearchClient::class, function () use ($config) {
            return new MeilisearchClient(
                $config['endpoint'],
                $config['key'],
                app(Factory::class),
            );
        });

        $this->app->bind(IndexerInterface::class, MeilisearchIndexer::class);
        $this->app->bind(SearchInterface::class, MeilisearchSearch::class);
    }
}
