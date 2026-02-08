@if($topic->tags->isNotEmpty())
    <div class="flex gap-2 items-center {{ $class ?? 'text-sm' }}">
        @foreach($topic->tags as $tag)
            <div class="flex rounded-sm overflow-hidden bg-card whitespace-nowrap">
                @if($tag->parent)
                    <span
                        class="px-[.5em]"
                        @if($tag->parent->color !== '#000000') style="background-color: {{ $tag->parent->color }}; color: #FFF;" @endif>
                                {{ $tag->parent->name }}
                            </span>
                @endif
                <a
                    class="pill px-[.5em] small pill-square pill-grey whitespace-nowrap bg-border/40"
                    @if($tag->color !== '#000000') style="background-color: {{ $tag->color }}; color: #FFF;" @endif>
                    {{ $tag->name }}
                </a>
            </div>
        @endforeach
    </div>
@endif
