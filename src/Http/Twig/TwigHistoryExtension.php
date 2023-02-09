<?php

namespace App\Http\Twig;

use App\Domain\Application\Entity\Content;
use App\Domain\History\Repository\ProgressRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigHistoryExtension extends AbstractExtension
{
    public function __construct(private readonly ProgressRepository $repository)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'show_history',
                $this->showHistory(...),
                ['is_safe' => ['html'], 'needs_context' => true]
            ),
        ];
    }

    /**
     * @param Content[]|ArrayCollection<Content> $contents
     */
    public function showHistory(array $context, array|Collection $contents): ?string
    {
        $user = $context['app']->getUser();
        if (null === $user) {
            return null;
        }
        if ($contents instanceof Collection) {
            $contents = $contents->toArray();
        }
        $progress = $this->repository->findForContents($user, $contents);
        $ids = [];
        foreach ($progress as $p) {
            $ids[$p->getContent()->getId()] = $p->getRatio();
        }
        $ids = json_encode($ids, JSON_THROW_ON_ERROR);

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
