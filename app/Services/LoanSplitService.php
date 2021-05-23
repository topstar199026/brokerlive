<?php

namespace App\Services;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Response;

class LoanSplitService  extends Controller
{
    public function getSomething($aba)
    {
        //return $dealId;
        return view('test');
            //->render();
    }
    // public static function getLoanSplits($dealId)
    // {
    //     //return $dealId.'hhhh';
    //     return view('test');
    // }

    public static function getLoanSplits($dealId)
    {
        return $dealId.'hhhh';
    }

    public static function checkTag($split, $tag)
    {
        return $split->has_tag($tag) ? 'checked' : '';
    }

    public static function getName($name)
    {
        return strlen($name) > 20 ? substr($name, 0, 20) . "..." : $name;
    }
}
