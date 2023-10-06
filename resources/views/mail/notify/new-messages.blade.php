@component('mail::header', ['url' => frontUrl()])
<div class="logo">
    <img class="logo" src="{{ frontUrl('static/assets/img/pinaheart_logo.png') }}" alt="">
</div>
@endcomponent
@component('mail::message')
# Dear {{ $user->first_name }},

@component('mail::panel')
Look who sent you a message.
@endcomponent

@foreach ($senders as $sender)
{{ $sender->first_name }}, {{ $sender->age }}, {{ optional($sender->location)->full_address }} <br>
@endforeach

Please click <a href="{{ frontUrl('messages') }}">here</a> to read your messages.

Good luck! <br>
Pinaheart team.

@endcomponent

@component('mail::footer')
    You received this email because of the notification settings of your Pinaheart account. If you wish to change your settings, click <a href="{{ frontUrl('settings/notifications') }}">here</a>
@endcomponent
