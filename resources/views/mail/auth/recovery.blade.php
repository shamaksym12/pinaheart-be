@component('mail::message')
# Reset password

To reset your password go to the link

@component('mail::button', ['url' => $link])
Button Text
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
