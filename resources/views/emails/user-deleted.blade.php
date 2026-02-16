<x-mail::message>
# Suppression de compte

- **Nom :** {{ $user->name }}
- **Email :** {{ $user->email }}

**Raison :**

{{ $reason }}

</x-mail::message>
