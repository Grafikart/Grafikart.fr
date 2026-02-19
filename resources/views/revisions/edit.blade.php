@extends('front')

@section('title', 'Proposer une modification')

@section('body')

    <div class="container pb-10 bg-background-light">
        <h1 class="text-page-title mb-4">Proposer une modification</h1>
        <p class="text-lg text-muted max-w-175">
            Vous avez remarqué une faute sur le contenu <strong
                class="text-foreground-title">{{ $target->title }}</strong>, n'hésitez pas à proposer votre modification
            pour apporter votre correction
        </p>
    </div>

    <div class="border-t bg-background pt-10">
        <div class="container">
            <form class="grid gap-4 max-w-200" method="post"
                  action="{{ route('revision.update', ['type' => $type, 'id' => $id]) }}">
                @csrf
                <x-molecules.field name="content" required label="Contenu" type="textarea" :value="$content" rows="20"/>
                <x-atoms.button>
                    <x-lucide-send/>
                    Envoyer la proposition
                </x-atoms.button>
            </form>
        </div>
    </div>
@endsection
