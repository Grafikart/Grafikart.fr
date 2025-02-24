<?php

namespace App\Http\Admin\Controller;

use App\Domain\Auth\Service\UserBanService;
use App\Domain\Auth\UserRepository;
use App\Domain\Comment\CommentRepository;
use App\Domain\Forum\Repository\MessageRepository;
use App\Domain\Forum\Repository\TopicRepository;
use App\Infrastructure\Spam\GeoIpService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class IpController extends BaseController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly CommentRepository $commentRepository,
        private readonly GeoIpService $ipService,
        private readonly TopicRepository $topicRepository,
        private readonly MessageRepository $messageRepository,
        private readonly UserBanService $banService,
        private readonly EntityManagerInterface $em,
    ) {
    }

    #[Route(path: '/ip/{ip}', name: 'ip', methods: ['GET'])]
    public function ip(
        string $ip,
    ): Response {
        $comments = $this->commentRepository
            ->queryByIp($ip)
            ->setMaxResults(15)
            ->getQuery()
            ->getResult();
        $location = $this->ipService->getLocation($ip);
        $users = $this->userRepository->findByIp($ip);
        $topics = $this->topicRepository->findLastByUsers($users);
        $messages = $this->messageRepository->findLastByUsers($users);

        return $this->render('admin/spam/ip.html.twig', [
            'ip' => $ip,
            'comments' => $comments,
            'location' => $location,
            'users' => $users,
            'topics' => $topics,
            'messages' => $messages,
        ]);
    }

    #[Route(path: '/ip/{ip}', methods: ['DELETE'])]
    public function block(string $ip): RedirectResponse
    {
        $this->commentRepository->queryByIp($ip)->delete()->getQuery()->execute();
        $users = $this->userRepository->findByIp($ip);
        foreach ($users as $user) {
            if (!$user->isPremium()) {
                $this->banService->ban($user);
            }
        }
        $this->em->flush();
        $this->addFlash('success', "L'ip a bien été bloquée");

        return $this->redirectBack('admin_ip', ['ip' => $ip]);
    }
}
