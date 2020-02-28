<?php

namespace App\Twig;

use App\Twig\CacheExtension\CacheableInterface;
use App\Twig\CacheExtension\CacheTokenParser;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\CacheItem;
use Twig\Extension\AbstractExtension;
use Twig\TokenParser\AbstractTokenParser;

class TwigCacheExtension extends AbstractExtension
{

    private AdapterInterface $cache;

    public function __construct(AdapterInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @return array<AbstractTokenParser>
     */
    public function getTokenParsers(): array
    {
        return [
            new CacheTokenParser()
        ];
    }

    /**
     * @param CacheableInterface|string|null|array $item
     */
    public function getCacheKey($item): string
    {
        if (empty($item)) {
            throw new \Exception('Clef de cache invalide');
        }
        if (is_string($item)) {
            return $item;
        }
        if (is_array($item)) {
            return implode('-', array_map(fn ($v) => $this->getCacheKey($v), $item));
        }
        if (!is_object($item)) {
            throw new \Exception("TwigCache : Impossible de serialiser une variable qui n'est pas un objet ou une chaine");
        }
        try {
            $updatedAt = $item->getUpdatedAt();
            $id = $item->getId();
            $className = get_class($item);
            $className = substr($className, strrpos($className, '\\') + 1);
            return $id . $className . $updatedAt->getTimestamp();
        } catch (\Error $e) {
            throw new \Exception("TwigCache : Impossible de serialiser l'objet pour le cache : \n" . $e->getMessage());
        }
    }

    /**
     * @param CacheableInterface|string $item
     */
    public function getCacheValue($item): ?string
    {
        /** @var CacheItem $item */
        $item = $this->cache->getItem($this->getCacheKey($item));
        return $item->get();
    }

    /**
     * @param CacheableInterface|string $item
     */
    public function setCacheValue($item, string $value): void
    {
        /** @var CacheItem $item */
        $item = $this->cache->getItem($this->getCacheKey($item));
        $item->set($value);
        $this->cache->save($item);
    }

}
