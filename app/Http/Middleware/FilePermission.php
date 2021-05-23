<?php

namespace App\Http\Middleware;

use Closure;

use App\Models\FileManagement;
use App\Models\Deal;

use App\Http\Controllers\Util\UserUtil;

class FilePermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $fileId = $request->route('id');

        $dealId = FileManagement::find($fileId)->deal_id;
        $existDeal = Deal::where('id', '=', $dealId)->whereIn('user_id', UserUtil::getBrockerIds())->first();

        if($existDeal)
            return $next($request) ;
        else
            return null;
        
    }
}
