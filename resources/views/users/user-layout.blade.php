@extends('front')

@php
$tabCls = 'flex items-center gap-2 py-4 aria-selected:border-b-primary w-max aria-selected:text-primary px-4 relative border-b-2 border-b-transparent aria-selected:border-b-primary aria-selected:bg-list-hover hover:bg-list-hover hover:text-primary transition-all';
$active = Route::currentRouteName();
@endphp

@section('body')

    <div class="container pb-10 bg-background-light">
        <h1 class="text-page-title mb-2">@yield('title')</h1>

        <p class="flex  items-center gap-2">
            <x-atoms.user-badge :user="$user"/>
            Inscrit depuis {{ $user->created_at->diffForHumans(syntax: \Carbon\CarbonInterface::DIFF_ABSOLUTE) }}
        </p>
    </div>

    <div class="container border-t bg-background-light/50 flex overflow-x-auto relative before:absolute before:left-0 before:right-0 before:bottom-0 before:h-px before:bg-border">
        <a href="{{ route('users.edit') }}" class="{{ $tabCls }}" @if($active === 'users.edit') aria-selected="true" @endif>
            <x-lucide-user-pen class="size-5"/>
            Profil
        </a>
        <a href="{{ route('users.history') }}" class="{{ $tabCls }}" @if($active === 'users.history') aria-selected="true" @endif>
            <x-lucide-video class="size-5"/>
            Historique
        </a>
        @if($user->created_at->lt('2026-05-30'))
        <a href="{{route('users.badges')}}" class="{{ $tabCls }}" @if($active === 'users.badges') aria-selected="true" @endif>
            <x-lucide-award class="size-5"/>
            Badges
        </a>
        @endif
        <a href="{{ route('transactions.index') }}" class="{{ $tabCls }}" @if($active === 'transactions.index') aria-selected="true" @endif>
            <x-lucide-inbox class="size-5"/>
            Factures
        </a>
        @if(\App\Domains\Revision\Revision::existsForUser($user))
            <a href="{{ route('revisions.index') }}" class="{{ $tabCls }}" @if($active === 'revisions.index') aria-selected="true" @endif>
                <x-lucide-file-pen-line class="size-5"/>
                Révisions
            </a>
        @endif

        <div class="flex ml-auto">
            @can('school-manage')
                <a href="{{ route('schools.show') }}" class="{{ $tabCls }}" @if($active === 'school') aria-selected="true" @endif>
                    <x-lucide-graduation-cap class="size-5"/>
                    École
                </a>
            @endcan
            @can('admin')
                <a href="{{ route('cms.dashboard') }}" class="{{ $tabCls }}" data-turbo="false">
                    <x-lucide-pencil class="size-5"/>
                    Administration
                </a>
            @endcan
        </div>
    </div>

    <div class="border-t bg-background pt-10 pb-20 container">
        @yield('content')
    </div>
@endsection
