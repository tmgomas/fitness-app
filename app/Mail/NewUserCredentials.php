<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewUserCredentials extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $username;
    public $password;

    /**
     * Create a new message instance.
     *
     * @param  User  $user
     * @param  string  $username
     * @param  string  $password
     * @return void
     */
    public function __construct(User $user, string $username, string $password)
    {
        $this->user = $user;
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Your New Account Credentials')
            ->view('emails.new-user-credentials');
    }
}
