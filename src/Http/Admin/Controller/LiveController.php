<?php

namespace App\Http\Admin\Controller;

use App\Core\Helper\Paginator\PaginatorInterface;
use App\Domain\Live\LiveCreatedEvent;
use App\Domain\Live\LiveRepository;
use App\Domain\Live\LiveService;
use App\Domain\Live\LiveSyncService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LiveController extends BaseController
{
    /**
     * @Route("/live", name="live_index", methods={"GET"})
     */
    public function index(
        LiveRepository $liveRepository,
        PaginatorInterface $paginator,
        LiveService $service
    ): Response {
        $lives = $paginator->paginate($liveRepository->queryAll());

        return $this->render('admin/live/index.html.twig', [
            'lives' => $lives,
            'live' => $service->getCurrentLive(),
        ]);
    }

    /**
     * @Route("/live/sync", name="live_sync", methods={"POST"})
     */
    public function sync(
        LiveSyncService $service,
        EntityManagerInterface $em,
        EventDispatcherInterface $dispatcher
    ): Response {
        $lives = $service->buildNewLives();
        array_map([$em, 'persist'], $lives);
        $em->flush();
        foreach ($lives as $live) {
            $dispatcher->dispatch(new LiveCreatedEvent($live));
        }
        $this->addFlash('success', 'Les nouveaux lives ont bien été importés');

        return $this->redirectToRoute('admin_live_index');
    }

    /**
     * @Route("/live", name="live_set", methods={"POST"})
     */
    public function update(LiveService $service, Request $request): Response
    {
        $service->setCurrentLive($request->get('live_id'));
        $this->addFlash('success', 'Le live a bien été enregistré');

        return $this->redirectToRoute('admin_live_index');
    }

    /**
     * @Route("/live", name="live_delete", methods={"DELETE"})
     */
    public function delete(LiveService $service, Request $request): Response
    {
        $service->setCurrentLive(null);
        $this->addFlash('success', 'Le live a bien été supprimé');

        return $this->redirectToRoute('admin_live_index');
    }
}
