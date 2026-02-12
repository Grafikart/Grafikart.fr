@extends('front')

@section('title', 'Notifications')

@section('body')

    <div class="container pb-10 bg-background-light">
        <h1 class="text-page-title mb-4">Notifications</h1>
    </div>

    <div class="border-t bg-background pt-10">
        <div class="container">
            @foreach($notifications as $notification)
                <div>
                    <p>
                        <a class="formatted ignore-br" href="{{ $notification->url }}">{!! $notification->message !!}</a>
                    </p>
                    <x-atoms.ago :time="$notification->created_at"/>
                </div>
            @endforeach
        </div>
    </div>
@endsection
