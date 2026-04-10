@extends('users.user-layout')

@section('title', 'Mes badges')

@section('content')
    <main class="mx-auto max-w-7xl pb-20">

        <x-atoms.alert type="info" class="mb-10">
            Vous ne pouvez pas débloquer de nouveaux badges depuis la nouvelle version du site mais les badges que vous aviez débloqué restent visible.
        </x-atoms.alert>

        <div class="grid grid-fill-180 gap-x-4 gap-y-8">
            @foreach ($badges as $badge)
                <div>
                    <div class="size-22 mx-auto">
                        @if ($badge->unlocked)
                            <div class="badge badge-{{ $badge->theme }}">
                                <img
                                    src="/uploads/badges/{{ $badge->image }}"
                                    width="160"
                                    height="160"
                                    alt=""
                                    class="relative z-2"
                                >
                            </div>
                        @else
                            <img src="/images/badge-placeholder.png" width="87" height="87" class="mx-auto" alt=""/>
                        @endif
                    </div>
                    <div @class([
                        'text-center text-xl font-bold mt-2',
                        'text-muted' => ! $badge->unlocked,
                    ])>{{ $badge->name }}</div>
                    <div class="text-muted text-center">{{ $badge->description }}</div>
                </div>
            @endforeach
        </div>
    </main>
@endsection

@section('head')
    <style>
        .badge {
            --offset: 0px;
            position: relative;
            width: 160px;
            height: 160px;
            transform: translate(-35px,-35px) scale(.6);
            background: url(/images/badges.png) top var(--offset, 0) left 0;
            transition: .4s;
        }
        .badge:hover {
            transform: translate(-35px,-35px) scale(.8);

            &::before {
                transform: scale(0.93);
            }
            &::after {
                transform: scale(0.95);
            }
        }
        .badge::before, .badge::after {
            content:'';
            transition: .4s;
            inset: 0;
            position: absolute;
            width: 160px;
            height: 160px;
            background: url(/images/badges.png) top var(--offset, 0) left 0;
        }
        .badge::before {
            background-position: top calc(var(--offset) - 160px) left 0;
        }
        .badge::after {
            background-position: top calc(var(--offset) - 320px) left 0;
        }
        .badge-grey {
            --offset: -480px;
        }
    </style>
@endsection
