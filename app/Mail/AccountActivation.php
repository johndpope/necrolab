<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Users;

class AccountActivation extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The subject of the message.
     *
     * @var string
     */
    public $subject = 'The NecroLab: Activate Your New Account';

    /**
     * @var \App\Users The user that was registered.
     */
    private $user;

    /**
     * @var string
     */
    private $verification_url;

    /**
     * Create a new message instance.
     *
     * @param \App\Users $user The user that was registered.
     * @param string $verification_url
     * @return void
     */
    public function __construct(Users $user, string $verification_url) {
        $this->user = $user;

        $this->verification_url = $verification_url;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.markdown.account_activation')
            ->with([
                'user' => $this->user,
                'verification_url' => $this->verification_url
            ]);
    }
}
