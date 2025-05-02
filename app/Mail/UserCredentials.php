<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

//To send the email to the user with the temporary password
class UserCredentials extends Mailable
{
    //To use the queueable, serializesModels and the credentials
    use Queueable, SerializesModels;

    //To store the credentials
    public $credentials;

    //To construct the credentials
    public function __construct($credentials)
    {
        $this->credentials = $credentials;
    }

    //To build the email
    public function build()
    {
        return $this->markdown('emails.user-credentials') //To use the email template
            ->subject('Your FYP System Credentials'); //To set the subject of the email
    }
}
