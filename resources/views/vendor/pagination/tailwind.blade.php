@php
    $linkClass = 'grid place-items-center size-8 rounded-sm hover:bg-primary/5';
    $navClass = 'flex items-center h-8 gap-1';
    $activeClass = 'border';
    $disabledClass = 'pointer-events-none opacity-50';
@endphp

@if ($paginator->hasPages())

        <ul class="flex w-full sm:w-max sm:mx-auto items-center gap-0.5 text-sm" aria-label="{{ __('Pagination Navigation') }}">
            {{-- Previous Page Link --}}
            <li>
                @if ($paginator->onFirstPage())
                    <span class="{{ $navClass }} {{ $disabledClass }}">
                        <x-lucide-chevron-left class="size-4" />
                        <span class="sm:hidden">Précédent</span>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="{{ $navClass }}" title="Précédent">
                        <x-lucide-chevron-left class="size-4" />
                        <span class="sm:hidden">Précédent</span>
                    </a>
                @endif
            </li>

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="hidden sm:block">
                        <span class="flex size-8 items-center justify-center">
                            <x-lucide-ellipsis class="size-4" />
                            <span class="sr-only">Plus de pages</span>
                        </span>
                    </li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        <li class="hidden sm:block">
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page" class="{{ $linkClass }} {{ $activeClass }}">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}" class="{{ $linkClass }}" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                    {{ $page }}
                                </a>
                            @endif
                        </li>
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            <li class="ml-auto md:ml-0">
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="{{ $navClass }}" title="Suivant">
                        <span class="sm:hidden">Page suivante</span>
                        <x-lucide-chevron-right class="size-4" />
                    </a>
                @else
                    <span class="{{ $navClass }} {{ $disabledClass }}">
                        <span class="sm:hidden">Page suivante</span>
                        <x-lucide-chevron-right class="size-4" />
                    </span>
                @endif
            </li>
        </ul>

@endif
