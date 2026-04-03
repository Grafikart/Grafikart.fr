<x-mail::message>
# Votre question a reçu une réponse

Votre question **{{ $question->title }}** sur le tutoriel **{{ $question->course->title }}** a reçu une réponse.

<x-mail::button :url="$url">
Voir la réponse
</x-mail::button>

Merci,<br>
{{ config('app.name') }}
</x-mail::message>
