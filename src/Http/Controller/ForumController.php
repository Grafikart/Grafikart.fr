<?php

namespace App\Http\Controller;

use App\Core\Helper\Paginator\PaginatorInterface;
use App\Domain\Auth\User;
use App\Domain\Forum\Entity\Forum;
use App\Domain\Forum\Entity\Tag;
use App\Domain\Forum\Entity\Topic;
use App\Domain\Forum\Repository\TagRepository;
use App\Domain\Forum\Repository\TopicRepository;
use App\Domain\Forum\TopicService;
use App\Http\Form\ForumTopicForm;
use App\Http\Security\ForumVoter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ForumController extends AbstractController
{
    private TagRepository $tagRepository;
    private TopicRepository $topicRepository;
    private PaginatorInterface $paginator;
    private TopicService $topicService;

    public function __construct(
        TagRepository $tagRepository,
        TopicRepository $topicRepository,
        PaginatorInterface $paginator,
        TopicService $topicService
    ) {
        $this->tagRepository = $tagRepository;
        $this->topicRepository = $topicRepository;
        $this->paginator = $paginator;
        $this->topicService = $topicService;
    }

    /**
     * @Route("/forum", name="forum")
     */
    public function index(): Response
    {
        return $this->tag(null);
    }

    /**
     * @Route("/forum/{slug<[a-z0-9\-]+>}-{id<\d+>}", name="forum_tag")
     */
    public function tag(?Tag $tag): Response
    {
        $topics = $this->paginator->paginate($this->topicRepository->queryAllForTag($tag));

        return $this->render('forum/index.html.twig', [
            'tags' => $this->tagRepository->findTree(),
            'topics' => $topics,
            'menu' => 'forum',
            'read_times' => $this->topicService->getReadTopicsIds(iterator_to_array($topics), $this->getUser()),
        ]);
    }

    /**
     * @Route("/forum/{id<\d+>}", name="forum_show")
     */
    public function show(Topic $topic): Response
    {
        $user = $this->getUser();
        if ($user) {
            $this->topicService->readTopic($topic, $user);
        }

        return $this->render('forum/show.html.twig', [
            'topic' => $topic,
            'menu' => 'forum',
        ]);
    }

    /**
     * @Route("/forum/new", name="forum_new")
     */
    public function create(Request $request): Response
    {
        $this->denyAccessUnlessGranted(ForumVoter::CREATE);
        /** @var User $user */
        $user = $this->getUser();
        $topic = (new Topic())->setContent($this->renderView('forum/template/placeholder.text.twig'));
        $topic->setAuthor($user);
        $form = $this->createForm(ForumTopicForm::class, $topic);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->topicService->createTopic($topic);
            $this->addFlash('success', 'Le sujet a bien été créé');

            return $this->redirectToRoute('forum_show', ['id' => $topic->getId()]);
        }

        return $this->render('forum/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/forum/{id<\d+>}/edit", name="forum_edit")
     */
    public function edit(Topic $topic, Request $request): Response
    {
        $this->denyAccessUnlessGranted(ForumVoter::UPDATE_TOPIC, $topic);
        $form = $this->createForm(ForumTopicForm::class, $topic);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->topicService->updateTopic($topic);
            $this->addFlash('success', 'Le sujet a bien été créé');

            return $this->redirectToRoute('forum_show', ['id' => $topic->getId()]);
        }

        return $this->render('forum/edit.html.twig', [
            'form' => $form->createView(),
            'id' => $topic->getId(),
        ]);
    }

    /**
     * @Route("/forum/search", name="forum_search")
     */
    public function search(
        Request $request,
        TopicRepository $topicRepository,
        \Knp\Component\Pager\PaginatorInterface $paginator
    ): Response {
        $q = $request->get('q');
        $page = $request->get('page', 1);
        [$items, $total] = $topicRepository->search($q, $page);
        $topics = $paginator->paginate([], 1, 10, ['whiteList' => []]);
        $topics->setTotalItemCount($total);
        $topics->setItems($items);
        $topics->setCurrentPageNumber($page);

        return $this->render('forum/search.html.twig', [
            'q' => $q,
            'topics' => $topics,
        ]);
    }
}
