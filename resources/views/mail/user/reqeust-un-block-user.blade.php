@component('mail::message')
# Request unblock account

Member id: {{ $user->profile_id }}

@if ( ! empty($device))
  Using your own computer or are you working on a friends: <br /> {{ $device }}
@endif

@if ( ! empty($location))
  Located: <br /> {{ $location }}
@endif

@if ( ! empty($comment))
  Comment: <br /> {{ $comment }}
@endif

@if ( ! empty($attach))
  Attach: {{ $attach }}
@endif

Thanks,<br>
{{ config('app.name') }}
@endcomponent
