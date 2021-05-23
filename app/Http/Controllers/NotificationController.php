<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;

use App\Http\Controllers\Util\FormatUtil;
use App\Http\Controllers\Util\UserUtil;
use App\Http\Controllers\Util\DealUtil;
use App\Http\Controllers\Util\ContactUtil;
use App\Http\Controllers\Util\LoanSplitUtil;
use App\Http\Controllers\Util\ReminderUtil;
use App\Http\Controllers\Util\JournalUtil;

class NotificationController extends Controller
{
    public function __construct()
    {
        
    }

    public function notification(Request $request)
    {
        $deal_id = $request->route('id');
        $_notifications = DealUtil::getDealNotify($deal_id);
        $notifications = count($_notifications) > 0 ? $_notifications[0] : null;
        return response()->json([
            'data' => $notifications,
            'error' => null,
            'status' => 'success',
        ]);
    }

    public function createNotification(Request $request, $id)
    {
        $data = Arr::except($request->all(), ['_token']);
        $notification = DealUtil::saveNotification($id, $data);        
        return $notification;
    }

    public function deleteNotification(Request $request, $id)
    {
        $notification = DealUtil::deleteNotification($id);        
        return $notification;
    }
}
