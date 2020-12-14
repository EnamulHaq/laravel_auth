<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetVerificationCode extends Mailable
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
            'Password Reset Verification Code sent at %1$s',
            config('app.name')
        );

        return $this
            ->subject($subject)
            ->view('emails.auth.password-reset-verification-code')
            ->with([
                'subject' => $subject,
                'first_name' => $this->user->first_name,
                'verification_code' => $this->user->verification_code
            ]);
    }
}
