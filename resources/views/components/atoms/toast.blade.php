@props(['type' => 'success', 'message' => ''])

@php
    $colors = cn([
        'bg-success-bg border-success-border text-success-text' => $type === 'success',
        'bg-error-bg border-error-border text-error-text' => $type === 'error',
    ]);
@endphp

<div
    {{ $attributes->merge(['class' => "leading-tight text-sm shadow-toast max-w-87 fixed top-6 left-0 right-0 mx-auto z-100 border p-4 px-3 rounded-lg flex items-center gap-2 transition-all duration-500 starting:opacity-0 starting:-translate-y-4 data-hide:opacity-0 data-hide:-translate-y-4 {$colors}"]) }}
>
    @if($type === 'success')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" height="20" width="20" class="flex-none"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"></path></svg>
    @endif
    <span>{{ $message ?: $slot }}</span>
    <button onclick="this.parentElement.setAttribute('data-hide', ''); //setTimeout(() => this.parentElement.remove(), 1000)" class="absolute top-0 left-0 -translate-35/100 size-5 rounded-full {{ $colors }} grid place-items-center">
        <x-lucide-x class="size-3"/>
    </button>
</div>
