<?php

namespace App\Core\Twig;

use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Système d'assets servant de remplacement à SymfonyEncore basé sur Vite.
 */
class TwigAssetExtension extends AbstractExtension
{
    private string $assetPath;
    private CacheItemPoolInterface $cache;
    const CACHE_KEY = 'asset_time';
    private RequestStack $requestStack;

    public function __construct(string $assetPath, CacheItemPoolInterface $cache, RequestStack $requestStack)
    {
        $this->assetPath = $assetPath;
        $this->cache = $cache;
        $this->requestStack = $requestStack;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('encore_entry_link_tags', [$this, 'link'], ['is_safe' => ['html']]),
            new TwigFunction('encore_entry_script_tags', [$this, 'script'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * Récupère la date de création des assets.
     */
    private function getTime(): int
    {
        $cached = $this->cache->getItem(self::CACHE_KEY);
        if (!$cached->isHit()) {
            $timeFile = $this->assetPath.'/time';
            $time = 0;
            if (file_exists($timeFile)) {
                $time = filemtime($timeFile);
            }
            $this->cache->save($cached->set($time));
        } else {
            $time = $cached->get();
        }

        return $time;
    }

    /**
     * Génère l'URL associé à un asset passé en paramètre.
     *
     * @param string $name Le nom du fichier à charger ("app.js" par exemple)
     */
    private function uri(string $name): string
    {
        $time = $this->getTime();
        $request = $this->requestStack->getCurrentRequest();
        if (0 === $time && $request) {
            $host = $request->getHost();

            return "http://{$host}:3000/{$name}";
        }

        return "/assets/$name?$time";
    }

    public function link(string $name): string
    {
        $uri = $this->uri($name.'.css');
        if (strpos($uri, ':3000')) {
            return ''; // Le CSS est chargé depuis le JS dans l'environnement de dev
        }

        return '<link rel="stylesheet" media="screen" href="'.$this->uri($name.'.css').'"/>';
    }

    public function script(string $name): string
    {
        $script = '<script src="'.$this->uri($name.'.js').'" type="module" defer></script>';

        // Si on est en mode développement on injecte le système de Hot Reload de vite
        $request = $this->requestStack->getCurrentRequest();
        if (0 === $this->getTime() && $request) {
            $host = $request->getHost();
            $script = <<<HTML
                <script type="module">
                import "http://{$host}:3000/vite/hmr"
                window.process = { env: { NODE_ENV: "development" }}
                </script>
                $script
            HTML;
        }

        return $script;
    }
}
