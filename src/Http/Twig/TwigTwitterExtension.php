<?php

namespace App\Http\Twig;

use App\Infrastructure\Twitter\TwitterService;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigTwitterExtension extends AbstractExtension
{
    private TwitterService $twitter;

    private CacheInterface $cache;

    public function __construct(TwitterService $twitter, CacheInterface $cache)
    {
        $this->twitter = $twitter;
        $this->cache = $cache;
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
