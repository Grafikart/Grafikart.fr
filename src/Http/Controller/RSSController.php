<?php

namespace App\Http\Controller;

use App\Domain\Application\Repository\ContentRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RSSController extends AbstractController
{
    #[Route(path: '/feed.rss', name: 'rss')]
    public function feed(ContentRepository $contentRepository): Response
    {
        $items = $contentRepository->findLatestPublished();
        $response = $this->render('feed/rss.xml.twig', [
            'items' => $items,
        ]);
        $response->headers->set('Content-Type', 'application/rss+xml; charset=utf-8');

        return $response;
    }
}
