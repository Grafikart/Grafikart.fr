<?php

namespace App\Http\Admin\Controller;

use App\Domain\Live\LiveRepository;
use App\Domain\Live\LiveSyncService;
use App\Helper\Paginator\PaginatorInterface;
use App\Infrastructure\Queue\Message\ServiceMethodMessage;
use App\Infrastructure\Youtube\YoutubeScopes;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class LiveController extends BaseController
{
    /**
     * @Route("/live", name="live_index", methods={"GET"})
     */
    public function index(
        LiveRepository $liveRepository,
        PaginatorInterface $paginator
    ): Response {
        $lives = $paginator->paginate($liveRepository->queryAll());

        return $this->render('admin/live/index.html.twig', [
            'lives' => $lives,
            'menu'  => 'live',
        ]);
    }

    /**
     * @Route("/live/sync", name="live_sync", methods={"POST", "GET"})
     */
    public function sync(
        \Google_Client $googleClient,
        MessageBusInterface $messageBus,
        Request $request
    ): Response {
        $url = $request->getUriForPath($request->getPathInfo());
        $googleClient->setRedirectUri($url);
        if (null === $request->get('code')) {
            return $this->redirect($googleClient->createAuthUrl(YoutubeScopes::READONLY));
        }
        $googleClient->fetchAccessTokenWithAuthCode($request->get('code'));
        $messageBus->dispatch(new ServiceMethodMessage(
            LiveSyncService::class,
            'sync',
            [$googleClient->getAccessToken()]
        ));
        $this->addFlash('success', 'Les nouveaux lives ont bien été importés');

        return $this->redirectToRoute('admin_live_index');
    }
}
