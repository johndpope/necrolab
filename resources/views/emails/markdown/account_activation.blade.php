@component('mail::message')
# You're almost done! Just confirm your email

Thank you for registering an account with The NecroLab! Please click the button below to verify your email address.

@component('mail::button', ['url' => $verification_url])
Activate Your Account
@endcomponent
@endcomponent