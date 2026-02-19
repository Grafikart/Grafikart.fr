@extends('users.user-layout')

@section('title', 'Mes révisions')

@section('content')

    <div>
        <section class="flex flex-col gap-4">
            @if($revisions->isEmpty())
                <p class="py-8 text-muted text-center text-lg">
                    Vous n'avez encore proposé aucune modification.
                </p>
            @else
                <table class="table">
                    <thead>
                    <tr>
                        <th class="uppercase text-muted">Article</th>
                        <th class="uppercase text-muted">Date</th>
                        <th class="uppercase text-muted">Statut</th>
                        <th class="uppercase text-muted">Commentaire</th>
                        <th class="uppercase text-muted"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($revisions as $revision)
                        <tr>
                            <td>
                                @if($revision->revisionable)
                                    <a href="{{ route('revision.edit', ['type' => $revision->revisionable_type, 'id' => $revision->revisionable_id]) }}"
                                       class="hover:underline">
                                        {{ $revision->revisionable->title }}
                                    </a>
                                @else
                                    <span class="text-muted">Contenu supprimé</span>
                                @endif
                            </td>
                            <td>{{ $revision->created_at->translatedFormat('j F Y') }}</td>
                            <td>
                                @if($revision->state === \App\Domains\Revision\RevisionStatus::Rejected)
                                    <x-atoms.badge variant="destructive">Rejetée</x-atoms.badge>
                                @elseif($revision->state === \App\Domains\Revision\RevisionStatus::Pending)
                                    <x-atoms.badge variant="info">En attente</x-atoms.badge>
                                @else
                                    <x-atoms.badge variant="success">Acceptée</x-atoms.badge>
                                @endif
                            </td>
                            <td>
                                @if($revision->comment)
                                    <p class="text-sm text-muted">{{ $revision->comment }}</p>
                                @endif
                            </td>
                            <td class="text-right">
                                @if($revision->state === \App\Domains\Revision\RevisionStatus::Pending && $revision->revisionable)
                                    <div class="flex items-center justify-end gap-2">
                                        <x-atoms.button size="sm" variant="secondary"
                                                        href="{{ route('revision.edit', ['type' => $revision->revisionable_type, 'id' => $revision->revisionable_id]) }}">
                                            <x-lucide-pencil class="size-3"/>
                                            Modifier
                                        </x-atoms.button>
                                        <form method="post" action="{{ route('revision.delete', $revision) }}">
                                            @csrf
                                            @method('DELETE')
                                            <x-atoms.button size="sm" variant="destructive" as="button" type="submit">
                                                <x-lucide-trash class="size-3"/>
                                                Supprimer
                                            </x-atoms.button>
                                        </form>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

                {{ $revisions->links() }}
            @endif
        </section>
    </div>

@endsection
