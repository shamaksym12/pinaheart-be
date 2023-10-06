@component('mail::message')
# Switch off

{{ $user->account_status_text }} member {{ $user->first_name .' '. $user->last_name }} switched off his profile

Member id: {{ $user->profile_id }}

Reason: {{ $reason }}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
