<?php

namespace App\Mail;

use App\Datas\MailData;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailHelper extends Mailable
{
    use Queueable, SerializesModels;

    public $mailData;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(MailData $mailData)
    {
        $this->mailData = $mailData;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->view('temps.password_changed')
            ->subject($this->mailData->subject)
            ->with([
                'name' => $this->mailData->userName,
                'new_password' => $this->mailData->content,
            ]);
    }
}
