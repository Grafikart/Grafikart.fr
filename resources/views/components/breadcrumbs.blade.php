<div {{ $attributes->class('breadcrumb flex items-center gap-2 text-sm text-muted') }}>
    @foreach ($items as $item)
        @if (is_iterable($item) && !isset($item['label']))
            <div class="flex items-center gap-1">
                @foreach ($item as $child)
                    <a itemprop="url" title="{{ $child['label'] }}" href="{{ $child['url'] }}" class="hover:text-foreground hover:underline transition-colors">
                        <span itemprop="title">{{ $child['label'] }}</span>
                    </a>
                    @if (!$loop->last)
                        <span class="breadcrumb-separator">&</span>
                    @endif
                @endforeach
            </div>
        @else
            <a itemprop="url" title="{{ $item['label'] }}" href="{{ $item['url'] }}" class="hover:text-foreground hover:underline transition-colors">
                <span itemprop="title">{{ $item['label'] }}</span>
            </a>
        @endif
        @if (!$loop->last)
            <x-lucide-chevron-right class="size-4 text-muted/50" />
        @endif
    @endforeach
</div>
