<?php

namespace App\Http\Admin\Controller;

use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Attribute\Route;

class CacheController extends BaseController
{
    #[Route(path: '/cache/clean', name: 'cache_clean', methods: ['POST'])]
    public function clean(CacheItemPoolInterface $cache): RedirectResponse
    {
        if ($cache->clear()) {
            $this->addFlash('success', 'Le cache a bien été supprimé');
        } else {
            $this->addFlash('danger', "Le cache n'a pas pu être supprimé");
        }

        return $this->redirectToRoute('admin_home');
    }
}
