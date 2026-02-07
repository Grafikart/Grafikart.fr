@extends('front', ['class' => 'bg-background-light'])

@section('body')
    <div class="container pb-10">
        <h1 class="text-page-title mb-4">@yield('title')</h1>
        @if(isset($description))
        <p class="text-lg text-muted max-w-2xl">
            Si vous avez des problèmes ou un bug dans votre code n'utilisez pas ce formulaire, utilisez plutôt le
            système de support présent sur les vidéos.
        </p>
        @endif
    </div>

    <div class="border-t bg-background pt-10">
        <div class="container prose prose-lg">
            @yield('content')
        </div>
    </div>
@endsection
