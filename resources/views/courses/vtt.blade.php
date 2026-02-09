WEBVTT

@foreach($chapters as $k => $chapter)
{{ $k + 1 }}
{{ $chapter['start'] }} --> {{ $chapter['end'] }}
{{ $chapter['title'] }}

@endforeach
