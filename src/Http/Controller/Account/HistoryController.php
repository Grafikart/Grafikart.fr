<?php

declare(strict_types=1);

namespace App\Http\Controller\Account;

use App\Domain\History\Entity\Progress;
use App\Domain\History\Repository\ProgressRepository;
use App\Helper\Paginator\PaginatorInterface;
use App\Http\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HistoryController extends AbstractController
{
    /**
     * @Route("/profil/historique", name="user_history")
     * @IsGranted("ROLE_USER")
     */
    public function index(
        ProgressRepository $progressRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        $query = $progressRepository->queryAllForUser($this->getUserOrThrow());
        $filter = $request->get('filter', 'progress');
        if ('completed' === $filter) {
            $query = $query
                ->andWhere('p.progress = :progress')
                ->setParameter('progress', Progress::TOTAL);
        } elseif ('progress' === $filter) {
            $query = $query
                ->andWhere('p.progress != :progress')
                ->setParameter('progress', Progress::TOTAL);
        }

        $progress = $paginator->paginate(
            $query->setMaxResults(16)->getQuery()
        );
        $filterOptions = [
            'all' => 'Tout',
            'completed' => 'Terminées',
            'progress' => 'En cours de visionnage',
        ];

        return $this->render('account/history.html.twig', [
            'items' => $progress,
            'filter' => $filter,
            'menu' => 'account',
            'options' => $filterOptions,
        ]);
    }
}
