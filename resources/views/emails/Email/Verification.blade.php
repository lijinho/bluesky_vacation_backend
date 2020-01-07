@component('mail::message')
<b>Hi,{{$first_name}}!<b>
Please enter the following verification code to verify this login attempt
<p style="text-align:center;font-size:40px;"><b>{{$email_code}}</b></p>

Thanks regard<br>
{{ config('app.name') }}
@endcomponent
