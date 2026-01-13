<?php

namespace App\Normalizer;

use App\Component\ObjectMapper\ObjectMapperInterface;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Psr\Container\ContainerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

readonly class PaginationNormalizer implements NormalizerInterface
{
    public function __construct(private UrlGeneratorInterface $urlGenerator, private ObjectMapperInterface $mapper, ContainerInterface $container)
    {
    }

    /**
     * @throws \Exception
     */
    public function normalize(mixed $data, ?string $format = null, array $context = []): array
    {
        if (!$data instanceof SlidingPagination) {
            throw new \Exception(sprintf('%s cannot be used to serialize %s', self::class, $data::class));
        }
        $firstItem = $data->getItems()[0] ?? null;
        $itemCls = $context['item'] ?? ($firstItem ? get_class($firstItem) : null);
        if (empty($itemCls)) {
            throw new \Exception('you must set an item property in the context to choose how to map each item');
        }

        $paginationData = $data->getPaginationData();
        $route = $data->getRoute();
        $params = $data->getParams();
        $pageParameterName = $data->getPaginatorOption('pageParameterName');

        $pages = [];
        foreach ($paginationData['pagesInRange'] as $page) {
            $pages[] = [
                'page' => $page,
                'url' => $this->urlGenerator->generate($route, parameters: array_merge($params, [$pageParameterName => $page])),
            ];
        }

        $items = array_map(fn (mixed $item) => $this->mapper->map($item, $itemCls), $data->getItems());

        return [
            'links' => $pages,
            'page' => $data->getPage(),
            'last' => $data->getPageCount(),
            'items' => $items,
        ];
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof SlidingPagination;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            SlidingPagination::class => true,
        ];
    }
}
