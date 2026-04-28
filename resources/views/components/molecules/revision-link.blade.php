@props(['model'])

@auth
    <div {{ $attributes }}>
        <a href="{{ route('revision.edit', ['type' => $model->getMorphClass(), 'id' => $model->id]) }}"
           class="text-muted text-sm hover:underline flex items-center gap-1 mt-4">
            <x-lucide-bug class="size-4 text-primary"/>
            Vous avez vu une erreur dans cet article ? Proposer une modification
        </a>
    </div>
@endauth
