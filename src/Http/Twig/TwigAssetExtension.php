<?php

namespace App\Http\Twig;

use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\HttpFoundation\Request;
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
    private bool $isProduction;
    private ?array $paths = null;
    private bool $polyfillLoaded = false;

    public function __construct(
        string $assetPath,
        string $env,
        CacheItemPoolInterface $cache,
        RequestStack $requestStack
    ) {
        $this->assetPath = $assetPath;
        $this->cache = $cache;
        $this->requestStack = $requestStack;
        $this->isProduction = 'prod' === $env;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('encore_entry_link_tags', [$this, 'link'], ['is_safe' => ['html']]),
            new TwigFunction('encore_entry_script_tags', [$this, 'script'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * Récupère le chemin des assets depuis le fichier manifest.json.
     */
    private function getAssetPaths(): array
    {
        if (null === $this->paths) {
            $cached = $this->cache->getItem(self::CACHE_KEY);
            if (!$cached->isHit()) {
                $manifest = $this->assetPath.'/manifest.json';
                if (file_exists($manifest)) {
                    $paths = json_decode((string) file_get_contents($manifest), true);
                    $this->cache->save($cached->set($paths));
                    $this->paths = $paths;
                } else {
                    $this->paths = [];
                }
            } else {
                $this->paths = $cached->get();
            }
        }

        return $this->paths;
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
        $script = $this->preload($name.'.js').'<script src="'.$this->uri($name.'.js').'" type="module" defer></script>';
        $request = $this->requestStack->getCurrentRequest();

        if (false === $this->polyfillLoaded && $request instanceof Request) {
            $userAgent = $request->headers->get('User-Agent') ?: '';
            if (strpos($userAgent, 'Safari') &&
                !strpos($userAgent, 'Chrome')) {
                $this->polyfillLoaded = true;
                $script = <<<HTML
                    <script src="//unpkg.com/@ungap/custom-elements" defer></script>
                    $script
                HTML;
            }
        }

        return $script;
    }

    /**
     * Add preload for a specific script.
     *
     * @param string $name Le nom du fichier à charger ("app.js" par exemple)
     */
    private function preload(string $name): string
    {
        if (!$this->isProduction) {
            return '';
        }

        $imports = $this->getAssetPaths()[$name]['imports'] ?? [];
        $preloads = [];

        foreach ($imports as $import) {
            $preloads[] = <<<HTML
              <link rel="modulepreload" href="/assets/$import">
            HTML;
        }

        return implode("\n", $preloads);
    }

    /**
     * Génère l'URL associé à un asset passé en paramètre.
     *
     * @param string $name Le nom du fichier à charger ("app.js" par exemple)
     */
    private function uri(string $name): string
    {
        if (!$this->isProduction) {
            $request = $this->requestStack->getCurrentRequest();

            return $request ? "http://{$request->getHost()}:3000/{$name}" : '';
        }

        $name = $this->getAssetPaths()[$name]['file'] ?? $this->getAssetPaths()[$name] ?? '';

        return "/assets/$name";
    }
}
