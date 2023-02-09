<?php

namespace App\Http\Admin\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;

class CacheController extends BaseController
{
    #[Route(path: '/cache/clean', name: 'cache_clean', methods: ['POST'])]
    public function clean(CacheInterface $cache): RedirectResponse
    {
        $this->addFlash('success', 'Le cache a bien été supprimé');
        $cache->clear();

        return $this->redirectToRoute('admin_home');
    }
}
