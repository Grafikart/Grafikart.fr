<?php

namespace App\Http\Admin\Controller;

use App\Domain\Forum\Repository\MessageRepository;
use App\Domain\Forum\Repository\TopicRepository;
use App\Infrastructure\Mailing\Mailer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class Emailcontroller extends BaseController
{
    /**
     * @Route("/email", name="email_preview")
     */
    public function index(): Response
    {
        return $this->render('admin/page/email.html.twig');
    }

    /**
     * @Route("/email/{format}", name="email")
     */
    public function email(
        string $format,
        Mailer $mailer,
        TopicRepository $topicRepository,
    MessageRepository $messageRepository
    ): Response {
        $topic = $topicRepository->findOneBy(['author' => $this->getUser()]);
        $message = $messageRepository->findOneBy(['author' => $this->getUser()]);
        $email = $mailer->createEmail('mails/forum/new_post.twig', [
            'author' => $this->getUser(),
            'is_topic_owner' => true,
            'topic' => $topic,
            'message' => $message,
        ]);
        if ('html' === $format) {
            return new Response((string) $email->getHtmlBody());
        }

        return new Response("<pre>{$email->getTextBody()}</pre>");
    }
}
