<?php

namespace App\Core\Twig;

use Knp\Bundle\PaginatorBundle\Helper\Processor;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigPaginationExtension extends AbstractExtension
{
    private Processor $processor;

    public function __construct(Processor $processor)
    {
        $this->processor = $processor;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('paginate_nav', [$this, 'renderNav'],
                ['is_safe' => ['html'], 'needs_environment' => true]),
            new TwigFunction('paginate', [$this, 'render'],
                ['is_safe' => ['html'], 'needs_environment' => true]),
            new TwigFunction('sort_by', [$this, 'sortBy'],
                ['is_safe' => ['html'], 'needs_environment' => true]),
        ];
    }

    public function render(
        Environment $env,
        SlidingPagination $pagination,
        array $queryParams = [],
        array $viewParams = []
    ): string {
        return $env->render(
            $pagination->getTemplate() ?: 'partials/pagination.html.twig',
            $this->processor->render($pagination, $queryParams, $viewParams)
        );
    }

    public function renderNav(
        Environment $env,
        SlidingPagination $pagination,
        array $queryParams = [],
        array $viewParams = []
    ): string {
        $pagination->setTemplate('partials/pagination-nav.html.twig');

        return $this->render($env, $pagination, $queryParams, $viewParams);
    }

    public function sortBy(
        Environment $env,
        SlidingPagination $pagination,
        string $title,
        string $key,
        array $options = [],
        array $params = [],
        ?string $template = null
    ): string {
        return $env->render(
            $template ?: (string) $pagination->getSortableTemplate(),
            $this->processor->sortable($pagination, $title, $key, $options, $params)
        );
    }
}
