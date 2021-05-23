<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

//use App\Http\Controllers\Helper\MailHelper;
use App\Mail\MailHelper;

use App\Models\User;

use App\Http\Controllers\Util\UserUtil;

use App\Datas\MailData;


class AuthController extends Controller
{
    public function __invoke(Request $request)
    {
        if (Auth::check()) return redirect()->intended('dashboard');
        else return view('pages.login', ['success' => false]);
    }

    public function selectAuthType(Request $request)
    {
        $login = $request->input('username');
        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $request->merge([$fieldType => $login]);
        switch ($fieldType)
        {
            case 'email':
                return true;
                break;
            case 'username':
                return false;
                break;
        }
    }

    public function login(Request $request)
    {
        $validator = $this->selectAuthType($request) ?
            $request->validate([
                'email'     => 'required',
                'password'  => 'required'
                //'password'  => 'required|min:6'
            ])
            :
            $request->validate([
                'username'     => 'required',
                'password'  => 'required'
                //'password'  => 'required|min:6'
            ])
        ;
        if (Auth::attempt($validator))
        {
            //return redirect()->intended('pipeline');
            return redirect()->intended('configuration');
        }
        else
        {
            return view('pages.login', ['success' => 'false']);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        return redirect('/login');
    }

    public function forgot(Request $request)
    {
        $email = $request->input('email');
        // Mail::raw('Now I know how to send emails with Laravel', function($message) use($email)
        // {
        //     $message->subject('Hi There!!');
        //     $message->from(config('mail.from.address'), config("app.name"));
        //     $message->to($email);
        // });

        $user = UserUtil::getUserDataByEmail($email);
        if($user)
        {
            $newPassword = Str::random(8);
            $_newPassword = Hash::make($newPassword);

            User::where('id', $user->userId)->update(['password'=>$_newPassword]);

            $mailData = new MailData();
            $mailData->fromEmail = config('mail.from.address');
            $mailData->userName = $user->userName;
            $mailData->toEmail = $user->userEmail;//'topstar199026@gmail.com';//$user->userEmail;
            $mailData->subject = 'Brokerlive - Password Reset';
            $mailData->mailType = 'RESET_LINK_TYPE';
            $mailData->content = $newPassword;

            //MailHelper::send($mailData);
            Mail::to($mailData->toEmail)->send(new MailHelper($mailData));

            return redirect()->intended('login');//->with('success','true');
        }
        else{
            return view('pages.forgot', ['success' => 'false']);
        }
    }
}
