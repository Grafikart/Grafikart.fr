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

<site-header class="[body:not(.has-drawer)_&]:container block text-foreground-title fixed top-0 left-0 right-0 z-50 transition-all [&+*]:pt-28">
    <div class="flex items-center gap-2 border-b py-3 font-semibold in-[.has-drawer]:px-4">
        @if($drawer === 'left')
            <div class="contents 3xl:hidden">
                <drawer-toggle class="p-2 -ml-2 cursor-pointer block hover:text-primary">
                    <x-lucide-panel-left-open class="size-4 lg:hidden drawer-visible:hidden drawer-hidden:block"/>
                    <x-lucide-panel-left-close
                        class="size-4 hidden lg:block drawer-visible:block drawer-hidden:hidden"/>
                </drawer-toggle>
                <x-atoms.separator class="h-4 mr-2" orientation="vertical"/>
            </div>
        @endif
        <div style="view-transition-name:header-left" class="flex items-center">
            <a href="/" class="text-foreground mr-6">
                <x-atoms.logo/>
            </a>

            <x-atoms.separator orientation="vertical" class="h-4 hidden md:block"/>

            {{-- Navigation --}}
            <nav class="items-center hidden md:flex" id="navigation">
                @foreach($navItems as $item)
                    <a
                        href="{{ $item['href'] }}"
                        @if(request()->is(ltrim($item['href'], '/') . '*')) aria-current="page" @endif
                        class="{{ cn([
                        'flex items-center gap-2 px-5 py-2 text-sm hover:text-primary transition-colors aria-[current=page]:text-primary',
                        'text-warning hover:text-warning/80 aria-[current=page]:text-warning' => $item['highlight'] ?? false,
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
        <div class="flex items-center gap-2 ml-auto" style="view-transition-name: header-right">
            @if($user)
            <site-notification class="size-6 grid place-items-center hover:text-primary transition-colors relative cursor-pointer" read-at="{{ $user->notifications_read_at->getTimestamp() ?? $user->created_at->getTimestamp() }}">
                <x-lucide-bell class="size-4"/>
            </site-notification>
            @endif
            <site-search class="size-6 grid place-items-center hover:text-primary transition-colors cursor-pointer">
                <x-lucide-search class="size-4"/>
            </site-search>

            <x-atoms.separator orientation="vertical" class="h-4"/>

            {{-- Auth --}}
            <div class="hidden lg:contents" id="navigation-right">
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
        <burger-menu class="size-6 grid md:hidden place-items-center hover:text-primary transition-colors relative cursor-pointer">
            <x-lucide-menu class="size-4"/>
        </burger-menu>

        @if($drawer === 'right')
            <div class="contents 3xl:hidden">
                <x-atoms.separator class="h-4 mr-2" orientation="vertical"/>
                <drawer-toggle class="p-2 -mr-2 cursor-pointer block hover:text-primary">
                    <x-lucide-panel-right-open class="size-4 lg:hidden drawer-visible:hidden drawer-hidden:block"/>
                    <x-lucide-panel-right-close
                        class="size-4 hidden lg:block drawer-visible:block drawer-hidden:hidden"/>
                </drawer-toggle>
            </div>
        @endif

    </div>
</site-header>
