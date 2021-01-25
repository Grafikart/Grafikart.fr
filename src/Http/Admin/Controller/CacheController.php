<?php

namespace App\Http\Admin\Controller;

use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class CacheController extends BaseController
{
    /**
     * @Route("/cache/clean", name="cache_clean", methods={"POST"})
     */
    public function clean(AdapterInterface $cache): RedirectResponse
    {
        $this->addFlash('success', 'Le cache a bien été supprimé');
        $cache->clear();

        return $this->redirectToRoute('admin_home');
    }
}
