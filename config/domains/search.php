<?php

declare(strict_types=1);

use App\Infrastructure\Search\IndexerInterface;
use App\Infrastructure\Search\Meilisearch\MeilisearchClient;
use App\Infrastructure\Search\SearchInterface;
use App\Infrastructure\Search\Typesense\TypesenseClient;
use App\Infrastructure\Search\Typesense\TypesenseIndexer;
use App\Infrastructure\Search\Typesense\TypesenseSearch;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set('typesense_key', '%env(resolve:TYPESENSE_KEY)%');

    $parameters->set('typesense_host', '%env(resolve:TYPESENSE_HOST)%');

    $parameters->set('meilisearch_default_host', 'localhost:7700');

    $parameters->set('empty', '');

    $parameters->set('meilisearch_key', '%env(default:empty:MEILISEARCH_KEY)%');

    $parameters->set('meilisearch_host', '%env(default:meilisearch_default_host:MEILISEARCH_HOST)%');

    $services = $containerConfigurator->services();

    $services->defaults()
        ->autowire();

    $services->set(TypesenseClient::class)
        ->args([
        '%typesense_host%',
        '%typesense_key%',
    ]);

    $services->set(MeilisearchClient::class)
        ->args([
        '%meilisearch_host%',
        '%meilisearch_key%',
    ]);

    $services->set(IndexerInterface::class, TypesenseIndexer::class);

    $services->set(SearchInterface::class, TypesenseSearch::class);
};
