<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;


use App\Http\Controllers\Util\UserUtil;


class CommonComposer
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
        $_currentUrl = str_replace(URL::to('/').'/', '', url()->current());
        $currentUrl = ucfirst($_currentUrl);

        $_path = explode('/',$_currentUrl);



        $_2ndPath = '.index';

        // if(count($_path) > 1) {
        //     $_2ndPath = '.'.$_path[1];
        // }

        $cssPath = 'common.css.' . $_path[0]. $_2ndPath;
        $jsPath = 'common.js.' . $_path[0]. $_2ndPath;

        $view->with('mainPath', $_path[0]);
        $view->with('cssPath', $cssPath);
        $view->with('jsPath', $jsPath);

        $userLogin = UserUtil::login();
        UserUtil::checkApiKey();
        $userInfo = UserUtil::getUserInfo();
        $userRole = Auth::user()->getUserRole();

        $view->with('_currentUrl', $_currentUrl);
        $view->with('currentUrl', $currentUrl);
        $view->with('userLogin', $userLogin);
        $view->with('userInfo', $userInfo);
        $view->with('userRole', $userRole);
    }
}
