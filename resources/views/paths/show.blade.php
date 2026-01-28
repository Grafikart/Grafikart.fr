@extends('front')

@section('title', $path->title)

@section('body')
    <div class="-mt-27 relative">
        <div class="absolute inset-0 h-27 bg-linear-to-b from-background to-background/0 z-2"></div>
        <path-detail path="{{ $path->toJson() }}" class="w-screen block h-screen"></path-detail>
    </div>
@endsection
