<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;


use App\Http\Controllers\Util\UserUtil;


class UserDataComposer
{
    public function __construct()
    {
        // Dependencies automatically resolved by service container...
    }

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $userLogin = UserUtil::login();
        $userInfo = UserUtil::getUserInfo();
        $userRole = Auth::user()->getUserRole();
        
        $view->with('userLogin', $userLogin);
        $view->with('userInfo', $userInfo);
        $view->with('userRole', $userRole);
    }
}
