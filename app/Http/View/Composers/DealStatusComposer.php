<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\URL;


use App\Http\Controllers\Util\DealUtil;

class DealStatusComposer
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
        $dealStatus = DealUtil::getStatus();
        $view->with('dealStatus', $dealStatus);
    }
}
