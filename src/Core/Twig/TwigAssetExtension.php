<?php

namespace App\Core\Twig;

use Psr\Cache\CacheItemPoolInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigAssetExtension extends AbstractExtension
{
    private string $assetPath;
    private CacheItemPoolInterface $cache;
    const CACHE_KEY = 'asset_time';

    public function __construct(string $assetPath, CacheItemPoolInterface $cache)
    {
        $this->assetPath = $assetPath;
        $this->cache = $cache;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('encore_entry_link_tags', [$this, 'link'], ['is_safe' => ['html']]),
            new TwigFunction('encore_entry_script_tags', [$this, 'script'], ['is_safe' => ['html']]),
        ];
    }

    private function uri($name)
    {
        $cached = $this->cache->getItem(self::CACHE_KEY);
        if (!$cached->isHit()) {
            $timeFile = $this->assetPath . '/time';
            $time = 0;
            if (file_exists($timeFile)) {
                $time = filemtime($timeFile);
            }
            $this->cache->save($cached->set($time));
        } else {
            $time = $cached->get();
        }

        if (0 === $time) {
            return "http://{$_SERVER['SERVER_NAME']}:3000/{$name}";
        }

        return "/assets/$name?$time";
    }

    public function link($name): string
    {
        $uri = $this->uri($name . '.css');
        if (strpos($uri, ':3000')) {
            return ''; // Le CSS est chargé depuis le JS dans l'environnement de dev
        }

        return '<link rel="stylesheet" media="screen" href="' . $this->uri($name . '.css') . '"/>';
    }

    public function script($name): string
    {
        return '<script src="' . $this->uri($name . '.js') . '" type="module" defer></script>';
    }
}
