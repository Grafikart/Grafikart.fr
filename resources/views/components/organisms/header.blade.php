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
                        'flex items-center gap-2 px-5 py-2 text-sm hover:text-primary transition-colors',
                        'text-yellow hover:text-yellow/80' => $item['highlight'] ?? false,
                    ])}}"
                >
                    @svg('lucide-' . $item['icon'], 'size-4')
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>


        <site-search class="p-2 hover:text-primary transition-colors ml-auto">
            <x-lucide-search class="size-4"/>
        </site-search>

        <x-atoms.separator orientation="vertical" class="h-4"/>

        {{-- Auth --}}
        @auth
            <a href="{{ route('cms.dashboard') }}"
               class="flex items-center gap-2 text-sm hover:text-primary transition-colors">
                <x-lucide-user-round  class="size-4"/>
                <span>{{ auth()->user()->name }}</span>
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="p-2 text-sm hover:text-primary transition-colors" title="Se déconnecter">
                    <x-lucide-log-out class="size-4"/>
                </button>
            </form>
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
