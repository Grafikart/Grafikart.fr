@extends('front')

@section('title', 'Notifications')

@section('body')

    <div class="container pb-10 bg-background-light">
        <h1 class="text-page-title mb-4">Notifications</h1>
    </div>

    <div class="border-t bg-background pt-10">
        <div class="container">
            @foreach($notifications as $notification)
                <div class="p-4 -mx-4 border-b relative">
                    <a href="{{ $notification->url }}" class="overlay hover:underline block">
                       {!! $notification->message !!}
                    </a>
                    <x-atoms.ago class="text-muted text-sm block" :date="$notification->created_at"/>
                </div>
            @endforeach
        </div>
    </div>
@endsection
