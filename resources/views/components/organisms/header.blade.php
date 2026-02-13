@php
    $navItems = [
        ['label' => 'Tutoriels', 'href' => route('courses.index', absolute: false), 'icon' => 'square-play'],
        ['label' => 'Cursus', 'href' => '/cursus', 'icon' => 'list-video'],
        ['label' => 'Formations', 'href' => '/formations', 'icon' => 'list-video'],
        ['label' => 'Premium', 'href' => '/premium', 'icon' => 'star', 'highlight' => true],
        ['label' => 'Blog', 'href' => '/blog', 'icon' => 'notebook-pen'],
    ];
    $user = auth()->user();
@endphp

<site-header class="[body:not(.has-sidebar)_&]:container block text-foreground-title fixed top-0 left-0 right-0 z-50 transition-all [&+*]:pt-28">
    <div class="flex items-center justify-between gap-2 border-b py-3 font-semibold in-[.has-sidebar]:px-4">
        <div style="view-transition-name:header-left" class="flex items-center">
            <a href="/" class="text-foreground mr-6">
                <x-atoms.logo/>
            </a>

            <x-atoms.separator orientation="vertical" class="h-4"/>

            {{-- Navigation --}}
            <nav class="flex items-center">
                @foreach($navItems as $item)
                    <a
                        href="{{ $item['href'] }}"
                        @if(request()->is(ltrim($item['href'], '/') . '*')) aria-current="page" @endif
                        class="{{ cn([
                        'flex items-center gap-2 px-5 py-2 text-sm hover:text-primary transition-colors aria-[current=page]:text-primary',
                        'text-yellow hover:text-yellow/80 aria-[current=page]:text-yellow' => $item['highlight'] ?? false,
                    ])}}"
                    >
                        @svg('lucide-' . $item['icon'], 'size-4')
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endforeach

                @if(is_live_running())
                    <a
                        href="/live"
                        @if(request()->is('live')) aria-current="page" @endif
                        class="flex items-center gap-2 px-5 py-2 text-sm text-red-500 hover:text-red-400 transition-colors aria-[current=page]:text-destructive"
                    >
                        @svg('lucide-video', 'size-4 animate-live')
                        <span>Live</span>
                    </a>
                @endif
            </nav>
        </div>
        <div class="flex items-center gap-2" style="view-transition-name: header-right">
            @if($user)
            <site-notification class="size-6 grid place-items-center hover:text-primary transition-colors relative" read-at="{{ $user->notifications_read_at->getTimestamp() ?? $user->created_at->getTimestamp() }}">
                <x-lucide-bell class="size-4"/>
            </site-notification>
            @endif
            <site-search class="size-6 grid place-items-center hover:text-primary transition-colors">
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
    </div>
</site-header>
