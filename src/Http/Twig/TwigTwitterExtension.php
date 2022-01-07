<?php

namespace App\Http\Twig;

use App\Infrastructure\Twitter\TwitterService;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigTwitterExtension extends AbstractExtension
{
    public function __construct(private readonly TwitterService $twitter, private readonly CacheInterface $cache)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('last_tweets', [$this, 'lastTweets']),
        ];
    }

    public function lastTweets(): array
    {
        return $this->cache->get('tweets', function (ItemInterface $item) {
            $item->expiresAfter(3600);

            return $this->twitter->getLastTweets();
        });
    }
}
