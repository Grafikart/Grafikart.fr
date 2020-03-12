<?php

namespace App\Twig;

use Knp\Bundle\PaginatorBundle\Helper\Processor;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Knp\Bundle\PaginatorBundle\Twig\Extension\PaginationExtension;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigPaginationExtension extends AbstractExtension
{

    private PaginationExtension $paginationExtension;
    private Processor $processor;

    public function __construct(Processor $processor)
    {
        $this->processor = $processor;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('pagination_nav', [$this, 'renderNav'],
                ['is_safe' => ['html'], 'needs_environment' => true]),
            new TwigFunction('pagination', [$this, 'render'],
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


}
