<?php

namespace App\Http\Controllers\Util;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

use DB;

use App\Models\UserRelation;

use App\Http\Controllers\Util\FormatUtil;
use App\Http\Controllers\Util\CommonDbUtil;
use App\Http\Controllers\Util\JournalUtil;


use App\Datas\JournalTemp;

class RelationUtil extends Controller
{
    public static function getTeamRelatedOrg()
    {
        return UserRelation::where('user_id', '=', Auth::id())
            ->where('type', '=', 3)
            ->first();
    }

    public static function getOrgIdByUserId($userId)
    {
        return UserRelation::where('user_id', '=', $userId)
            ->where('type', '=', 3)
            ->first();
    }
}
