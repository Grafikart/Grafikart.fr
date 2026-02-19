@props(['model'])

@auth
    <div {{ $attributes->merge(['class' => 'max-w-200 mx-auto px-4 py-4']) }}>
        <a href="{{ route('revision.edit', ['type' => $model->getMorphClass(), 'id' => $model->id]) }}"
           class="text-muted text-sm hover:underline flex items-center gap-1">
            <x-lucide-pencil class="size-3"/>
            Proposer une modification
        </a>
    </div>
@endauth
