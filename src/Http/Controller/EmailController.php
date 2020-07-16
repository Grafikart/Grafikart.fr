<?php

namespace App\Http\Controller;

use App\Domain\Forum\Repository\TopicRepository;
use App\Infrastructure\Mailing\Mailer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EmailController
{

    /**
     * @Route("/testemail/{format}")
     */
    public function email (string $format, Mailer $mailer, TopicRepository $topicRepository) {
        $topic = $topicRepository->find(11090);
        $message = $mailer->createEmail('mails/forum/new_post.twig', [
            'is_topic_owner' => true,
            'author' => $topic->getAuthor(),
            'topic' => $topic,
            'message' => $topic->getMessages()[0]
        ]);
        return new Response($format === 'html' ? $message->getHtmlBody() : $message->getTextBody());
    }

}
