<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TutorConfirmationMail extends Mailable
{
    use SerializesModels;

    public $application;

    public function __construct($application)
    {
        $this->application = $application;
    }

    public function build()
    {
        return $this->view('emails.tutor_confirmation')
                    ->with([
                        'application_id' => $this->application->ApplicationID,
                        'tutor_name' => $this->application->tutor_name,  // Assuming there's a tutor name
                        'amount' => $this->application->amount,
                    ]);
    }
}
