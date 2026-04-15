@extends('front')

@section('title', $path->title)

@section('head')
    <style>
        site-header {
            background: linear-gradient(to top, transparent, var(--background) 20%)!important;
        }
    </style>
@endsection

@section('body')
    <div class="-mt-27 relative">
        <div class="absolute inset-0 h-27 bg-linear-to-b from-background to-background/0 z-2"></div>
        <path-detail path="{{ $path->toJson() }}" completednodeids="{{ json_encode($completedNodeIds) }}" class="w-screen block h-screen"></path-detail>
    </div>

    @can('edit', $path)
        <x-atoms.floating-button href="{{ route('cms.paths.edit', $path->id) }}">
            <x-lucide-edit/>
            Editer
        </x-atoms.floating-button>
    @endcan
@endsection
