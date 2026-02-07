@props(['as' => 'div'])

<{{ $as  }} {{$attributes->merge(['class' => "rounded-md bg-border/70 p-1 w-max flex items-center gap-1"]) }}>
    {{ $slot }}
</{{ $as }}>
