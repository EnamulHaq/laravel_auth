<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use App\Models\User;
use Illuminate\Queue\SerializesModels;

class EmailVerificationCode extends Mailable
{
    use Queueable, SerializesModels;

    protected $user;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = sprintf(
            'Verify your email address at %1$s',
            config('app.name')
        );

        return $this
            ->subject($subject)
            ->view('emails.auth.email-verification-code')
            ->with([
                'subject' => $subject,
                'first_name' => $this->user->first_name,
                'verification_code' => $this->user->verification_code
            ]);
    }
}
