@component('mail::message')
# Invitation à rejoindre un groupe

Vous avez été invité à rejoindre un groupe de cotisation.

@component('mail::button', ['url' => url("/invitations/accept/{$token}")])
Accepter l'invitation
@endcomponent

Merci,<br>
L’équipe Cotisation
@endcomponent