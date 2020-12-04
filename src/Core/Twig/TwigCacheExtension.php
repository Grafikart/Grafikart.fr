<?php

namespace App\Core\Twig;

use App\Core\Orm\IterableQueryBuilder;
use App\Core\Twig\CacheExtension\CacheableInterface;
use App\Core\Twig\CacheExtension\CacheTokenParser;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\CacheItem;
use Twig\Extension\AbstractExtension;
use Twig\TokenParser\AbstractTokenParser;

class TwigCacheExtension extends AbstractExtension
{
    private AdapterInterface $cache;
    private bool $active;

    public function __construct(AdapterInterface $cache, $active = true)
    {
        $this->cache = $cache;
        $this->active = $active;
    }

    /**
     * @return array<AbstractTokenParser>
     */
    public function getTokenParsers(): array
    {
        return [
            new CacheTokenParser(),
        ];
    }

    /**
     * @param CacheableInterface|string|array|bool|null $item
     */
    public function getCacheKey($item): string
    {
        if (is_bool($item)) {
            return $item ? '1' : '0';
        }
        if (empty($item)) {
            throw new \Exception('Clef de cache invalide');
        }
        if (is_string($item)) {
            return $item;
        }
        if (is_array($item)) {
            return implode('-', array_map(fn ($v) => $this->getCacheKey($v), $item));
        }
        if ($item instanceof IterableQueryBuilder) {
            $item = $item->getFirstResultOnly();
            if (null === $item) {
                return 'noresult';
            }
        }
        if (!is_object($item) || !($item instanceof CacheableInterface)) {
            throw new \Exception("TwigCache : Impossible de serialiser une variable qui n'est pas un objet ou une chaine");
        }
        try {
            $updatedAt = $item->getUpdatedAt() ?: new \DateTimeImmutable('@0');
            $id = $item->getId() ?: '0';
            $className = get_class($item);
            $className = substr($className, strrpos($className, '\\') + 1);

            return $id.$className.$updatedAt->getTimestamp();
        } catch (\Error $e) {
            throw new \Exception("TwigCache : Impossible de serialiser l'objet pour le cache : \n".$e->getMessage());
        }
    }

    /**
     * @param CacheableInterface|string $item
     */
    public function getCacheValue($item): ?string
    {
        if (!$this->active) {
            return null;
        }
        /** @var CacheItem $item */
        $item = $this->cache->getItem($this->getCacheKey($item));

        return $item->get();
    }

    /**
     * @param CacheableInterface|string $item
     */
    public function setCacheValue($item, string $value): void
    {
        if (!$this->active) {
            return;
        }
        /** @var CacheItem $item */
        $item = $this->cache->getItem($this->getCacheKey($item));
        $item->set($value);
        $this->cache->save($item);
    }
}
