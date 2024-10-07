<?php

namespace App\Http\Controller\Social;

use App\Domain\Live\LiveService;
use App\Helper\OptionManagerInterface;
use App\Http\Controller\AbstractController;
use App\Infrastructure\Twitch\TwitchAPI;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TwitchController extends AbstractController
{
    #[Route('/twitch/webhook', methods: ['POST'])]
    public function webhook(Request $request, TwitchAPI $api, OptionManagerInterface $optionManager): Response
    {
        $content = $request->getContent();
        $json = json_decode($content, true);
        if (isset($json['challenge'])) {
            return new Response($json['challenge'], 200);
        }
        if (!$api->validateSignature($request)) {
            return new Response('Invalid signature', 403);
        }
        if ($json['subscription']['type'] === 'stream.online') {
            $optionManager->set(LiveService::OPTION_KEY, (new \DateTimeImmutable('-10 minutes'))->format('Y-m-d H:i:s'));
        }
        if ($json['subscription']['type'] === 'stream.offline') {
            $optionManager->set(LiveService::OPTION_KEY, (new \DateTimeImmutable('-1 days'))->format('Y-m-d H:i:s'));
        }

        return new Response('Event received', 200);
    }
}
