<?php

namespace App\Core\Twig;

use App\Domain\Application\Entity\Content;
use App\Domain\History\HistoryService;
use App\Domain\History\Repository\ProgressRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigHistoryExtension extends AbstractExtension
{

    private ProgressRepository $repository;

    public function __construct(ProgressRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'show_history',
                [$this, 'showHistory'],
                ['is_safe' => ['html'], 'needs_context' => true]
            )
        ];
    }

    /**
     * @param Content[]|ArrayCollection<Content> $contents
     * @return string|null
     */
    public function showHistory(array $context, $contents): ?string
    {
        $user = $context['app']->getUser();
        if ($user === null) {
            return null;
        }
        if ($contents instanceof Collection) {
            $contents = $contents->toArray();
        }
        $progress = $this->repository->findForContents($user, $contents);
        $ids = [];
        foreach($progress as $p) {
            if ($content = $p->getContent()) {
                $ids[$content->getId()] = $p->getPercent();
            }
        }
        $ids = json_encode($ids);
        return <<<HTML
        <script>
          (function () {
            function a () {
              Grafikart.showHistory($ids)
              window.removeEventListener("turbolinks:load", a)
            }
            window.addEventListener("turbolinks:load", a)
          })()

        </script>
        HTML;
    }

}
