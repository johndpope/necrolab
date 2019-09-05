<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Lang;
use App\Mail\AccountActivation;

class VerifyEmailQueued extends VerifyEmail implements ShouldQueue {
    use Queueable;

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \App\Mail\AccountActivation
     */
    public function toMail($notifiable) {
        $verification_url = $this->verificationUrl($notifiable);

        return (new AccountActivation($notifiable, $verification_url))->to($notifiable->email);
    }
}