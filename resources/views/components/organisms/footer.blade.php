@php
    $socials = [
        ['label' => 'YouTube', 'href' => 'https://www.youtube.com/user/grafikarttv', 'icon' => 'youtube'],
        ['label' => 'GitHub', 'href' => 'https://github.com/Grafikart', 'icon' => 'github', 'class' => 'dark:brightness-1000'],
        ['label' => 'Twitter', 'href' => 'https://twitter.com/grafikart_fr', 'icon' => 'twitter'],
        ['label' => 'Discord', 'href' => 'https://discord.gg/rAuuD7Q', 'icon' => 'discord'],
        ['label' => 'Twitch', 'href' => 'https://www.twitch.tv/grafikart', 'icon' => 'twitch'],
        ['label' => 'Facebook', 'href' => 'https://www.facebook.com/grafikartfr', 'icon' => 'facebook'],
        ['label' => 'RSS', 'href' => '/rss', 'icon' => 'rss'],
    ];
@endphp

<div class="h-20 bg-background"></div>

<footer class="mt-auto border-t-4 border-border py-12">
    <div class="container grid grid-cols-1 gap-8 md:grid-cols-[440fr_375fr]">
        <div class="max-w-110">
            <h5 class="text-xl font-bold text-foreground-title mb-4">Me retrouver</h5>
            <p class="text-muted mb-4">
                Après avoir appris sur Internet quoi de plus normal que de partager à son tour ? Passionné par le web depuis un peu plus de {{ date('Y') - 2005 }} ans maintenant j'aime partager mes compétences et mes découvertes avec les personnes qui ont cette même passion pour le web
            </p>
            <div class="flex items-center gap-3">
                @foreach($socials as $social)
                    <a href="{{ $social['href'] }}" title="{{ $social['label'] }}" class="hover:opacity-70 transition-opacity">
                        <img src="/images/social/{{ $social['icon'] }}.svg" alt="{{ $social['label'] }}" width="20" height="20" loading="lazy" @class($social['class'] ?? '') />
                    </a>
                @endforeach
                <a href="https://www.infomaniak.com/goto/fr/hosting.managed-cloud?utm_term=59f74db50448d" class="ml-auto">
                    <img src="/images/badge-infomaniak.svg" alt="" loading="lazy" width="150" />
                </a>
            </div>
        </div>

        {{-- Contact --}}
        <div class="max-w-93 md:ml-auto">
            <h5 class="text-xl font-bold text-foreground-title mb-4">Me contacter</h5>
            <ul class="space-y-1 text-muted mb-6">
                <li><a href="/contact" class="flex items-center gap-2 hover:text-primary transition-colors"><x-lucide-mail class="size-4" /> Par email</a></li>
                <li><a href="/tchat" class="flex items-center gap-2 hover:text-primary transition-colors"><x-lucide-message-circle class="size-4" /> Tchat</a></li>
                <li><a href="https://www.youtube.com/user/grafikarttv" class="flex items-center gap-2 hover:text-primary transition-colors"><x-lucide-video class="size-4" /> Chaine youtube</a></li>
                <li><a href="/env" class="flex items-center gap-2 hover:text-primary transition-colors"><x-lucide-info class="size-4" /> A propos</a></li>
                <li><a href="/confidentialite" class="flex items-center gap-2 hover:text-primary transition-colors"><x-lucide-fingerprint class="size-4" /> Politique de confidentialité</a></li>
            </ul>
            <h5 class="text-xl font-bold text-foreground-title mb-4">Thème</h5>
            <theme-switcher/>
        </div>
    </div>
</footer>
