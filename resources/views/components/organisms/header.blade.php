@php
    $navItems = [
        ['label' => 'Tutoriels', 'href' => route('courses.index'), 'icon' => 'square-play'],
        ['label' => 'Cursus', 'href' => '/cursus', 'icon' => 'list-video'],
        ['label' => 'Formations', 'href' => '/formations', 'icon' => 'list-video'],
        ['label' => 'Premium', 'href' => '/premium', 'icon' => 'star', 'highlight' => true],
        ['label' => 'Blog', 'href' => '/blog', 'icon' => 'notebook-pen'],
    ];
@endphp

<header class="mb-10 text-foreground-title z-10 relative">
    <div class="container flex items-center gap-2 border-b py-2 pt-6 font-semibold">
        <a href="/" class="text-foreground mr-4">
            <x-atoms.logo/>
        </a>

        <x-atoms.separator orientation="vertical" class="h-4"/>

        {{-- Navigation --}}
        <nav class="flex items-center">
            @foreach($navItems as $item)
                <a
                    href="{{ $item['href'] }}"
                    class="{{ cn([
                        'flex items-center gap-2 px-5 py-2 text-sm transition-colors hover:text-primary',
                        'text-yellow hover:text-yellow/80' => $item['highlight'] ?? false,
                    ])}}"
                >
                    @svg('lucide-' . $item['icon'], 'size-4')
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>

        <button type="button" class="p-2 hover:text-primary transition-colors ml-auto">
            <x-lucide-search class="size-4"/>
        </button>

        <x-atoms.separator orientation="vertical" class="h-4"/>

        {{-- Auth --}}
        @auth
            <a href="{{ route('cms.dashboard') }}"
               class="flex items-center gap-2 text-sm text-muted hover:text-foreground transition-colors">
                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
                <span>{{ auth()->user()->name }}</span>
            </a>
        @else
            <div class="flex items-center gap-1">
                <a href="{{ route('register') }}" class="flex items-center gap-2 hover:text-primary transition-colors">
                    <x-lucide-user-round-plus class="size-4"/>
                    <span>S'inscrire</span>
                </a>
                <span class="text-muted">·</span>
                <a href="{{ route('login') }}" class="hover:text-primary transition-colors">
                    Se connecter
                </a>
            </div>
        @endauth
    </div>
</header>
