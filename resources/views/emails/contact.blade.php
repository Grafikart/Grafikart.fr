<x-mail::message>
# Nouveau message de contact

**Nom :** {{ $data->name }}  
**Email :** {{ $data->email }}

**Message :**

{{ $data->content }}

</x-mail::message>
