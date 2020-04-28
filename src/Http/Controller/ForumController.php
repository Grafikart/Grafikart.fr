<?php

namespace App\Http\Controller;

use App\Core\Helper\Paginator\PaginatorInterface;
use App\Domain\Forum\Entity\Forum;
use App\Domain\Forum\Entity\Tag;
use App\Domain\Forum\Entity\Topic;
use App\Domain\Forum\Repository\CategoryRepository;
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

    public function __construct(TagRepository $tagRepository, TopicRepository $topicRepository, PaginatorInterface $paginator)
    {
        $this->tagRepository = $tagRepository;
        $this->topicRepository = $topicRepository;
        $this->paginator = $paginator;
    }

    /**
     * @Route("/forum", name="forum")
     */
    public function index(TagRepository $tagRepository): Response
    {
        return $this->tag(null);
    }

    /**
     * @Route("/forum/new", name="forum_new")
     */
    public function create(Request $request, TopicService $service): Response
    {
        $this->denyAccessUnlessGranted(ForumVoter::CREATE);
        $topic = (new Topic())->setContent($this->renderView('forum/template/placeholder.text.twig'));
        $topic->setAuthor($this->getUser());
        $form = $this->createForm(ForumTopicForm::class, $topic);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $service->createTopic($topic);
            $this->addFlash('success', 'Le sujet a bien été créé');
            return $this->redirectToRoute('forum');
        }
        return $this->render('forum/new.html.twig', [
            'form' => $form->createView()
        ]);
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
            'menu' => 'forum'
        ]);
    }
    /**
     * @Route("/forum/{id<\d+>}", name="forum_show")
     */
    public function show(Topic $topic): Response
    {
        return $this->render('forum/show.html.twig', [
            'topic' => $topic,
            'messages' => $topic->getMessages(),
            'menu' => 'forum'
        ]);
    }

}
