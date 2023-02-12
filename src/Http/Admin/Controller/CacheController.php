<?php

namespace App\Http\Admin\Controller;

use App\Http\Twig\TwigAssetExtension;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;

class CacheController extends BaseController
{
    #[Route(path: '/cache/clean', name: 'cache_clean', methods: ['POST'])]
    public function clean(CacheItemPoolInterface $cache, CacheInterface $sfCache): RedirectResponse
    {
        $this->addFlash('success', 'Le cache a bien été supprimé');
        $cache->clear();
        // Nettoie le cache des assets
        $sfCache->delete(TwigAssetExtension::CACHE_KEY);

        return $this->redirectToRoute('admin_home');
    }
}
