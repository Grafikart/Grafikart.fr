@php
    $linkClass = 'grid place-items-center size-8 rounded-sm hover:bg-primary/5';
    $activeClass = 'border';
    $disabledClass = 'pointer-events-none opacity-50';
@endphp

@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="mx-auto flex w-full justify-center text-sm">
        <ul class="flex items-center gap-0.5">
            {{-- Previous Page Link --}}
            <li>
                @if ($paginator->onFirstPage())
                    <span class="{{ $linkClass }} {{ $disabledClass }} h-8 gap-1.5 px-2.5 w-auto">
                        <x-lucide-chevron-left class="size-4" />
                        <span class="hidden">Précédent</span>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="{{ $linkClass }} h-8 gap-1.5 px-2.5 w-auto" title="Précédent">
                        <x-lucide-chevron-left class="size-4" />
                    </a>
                @endif
            </li>

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li>
                        <span class="flex size-8 items-center justify-center">
                            <x-lucide-ellipsis class="size-4" />
                            <span class="sr-only">Plus de pages</span>
                        </span>
                    </li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        <li>
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
            <li>
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="{{ $linkClass }} h-8 gap-1.5 px-2.5 w-auto" title="Suivant">
                        <x-lucide-chevron-right class="size-4" />
                    </a>
                @else
                    <span class="{{ $linkClass }} {{ $disabledClass }} h-8 gap-1.5 px-2.5 w-auto">
                        <x-lucide-chevron-right class="size-4" />
                    </span>
                @endif
            </li>
        </ul>
    </nav>
@endif
