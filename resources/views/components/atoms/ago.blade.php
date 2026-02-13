@props(['date'])
<time-ago time="{{ $date->getTimestamp() }}" {{ $attributes }}>{{ $date->diffForHumans() }}</time-ago>
