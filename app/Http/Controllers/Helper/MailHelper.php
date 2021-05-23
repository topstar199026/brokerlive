<?php

namespace App\Http\Controllers\Helper;

use App\Http\Controllers\Controller;

use Mail;

class MailHelper extends Controller
{
    public static function send($mailData)
    {
        if($mailData && $mailData->email)
            Mail::send('emails.reminder', ['mailData' => $mailData], function ($m) use ($mailData) {
                $m->from('support@app.com', 'Your Application');
                $m->to($mailData->email, $mailData->username)
                    ->subject($mailData->subject);
            });
    }
}
