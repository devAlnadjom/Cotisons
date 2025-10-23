@component('mail::message')
# Invitation à rejoindre un groupe

Vous avez été invité à rejoindre le groupe **{{ $groupName ?? 'Cotisation' }}**.

@component('mail::button', ['url' => url("/invitations/accept/{$token}")])
Accepter l'invitation
@endcomponent

Si le bouton ne fonctionne pas, copiez ce lien dans votre navigateur :  
{{ url("/invitations/accept/{$token}") }}

Merci,<br>
L’équipe Cotisation
@endcomponent
