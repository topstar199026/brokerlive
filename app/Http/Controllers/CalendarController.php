<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;

use Response;

use App\Http\Controllers\Util\CalendarUtil;
use App\Http\Controllers\Util\FormatUtil;
use App\Http\Controllers\Util\UserUtil;
use App\Http\Controllers\Util\DealUtil;
use App\Http\Controllers\Util\ContactUtil;
use App\Http\Controllers\Util\LoanSplitUtil;
use App\Http\Controllers\Util\ReminderUtil;
use App\Http\Controllers\Util\JournalUtil;
use App\Http\Controllers\Util\LenderUtil;
use App\Http\Controllers\Util\PreferenceUtil;

use App\Datas\SelectData;
use App\Datas\DealAttribute;

class CalendarController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    public function index(Request $request)
    {
        //$resData['page_type'] = 'team';

        $data = Arr::except($request->all(), ['_token']);

        $fromDate = Arr::exists($data, 'from') ? data_get($data, 'from') : date('m/d/Y');
        $toDate = Arr::exists($data, 'to') ? data_get($data, 'to') : date('m/d/Y');
        $fromDate = date('d M Y', strtotime($fromDate));
        $toDate = date('d M Y', strtotime($toDate));
        $resData['fromDate'] = $fromDate;
        $resData['toDate'] = $toDate;

        $attributesStatus = new DealAttribute;
        $attributesStatus->id = 'reminder-filter-status';
        $attributesStatus->name = 'reminder-filter-status';
        $attributesStatus->class = 'multiselect';
        $resData['attributesStatus'] = $attributesStatus;

        $valuesStatus = DealUtil::getStatusForSelect();
        $resData['valuesStatus'] = $valuesStatus;

        $attributesLender = new DealAttribute;
        $attributesLender->id = 'reminder-filter-lender';
        $attributesLender->name = 'reminder-filter-lender';
        $attributesLender->class = 'multiselect';
        $resData['attributesLender'] = $attributesLender;

        $valuesLender = LenderUtil::getLendersForSelect();
        $resData['valuesLender'] = $valuesLender;

        $preferences = PreferenceUtil::getPreferenceJson();
        $resData['preferences'] = $preferences;

        return view('pages.calendar.index', $resData);
    }

    // public function calendar(Request $request)
    // {
    //     $data = Arr::except($request->all(), ['_token']);

    //     $remindersByDate = CalendarUtil::getRemindersByDate($data);

    //     $response = CalendarUtil::transformRemindersData($remindersByDate);

    //     if(CalendarUtil::isTodayIncluded($data))
    //     {
    //         $overdue = CalendarUtil::getOverdueCount($data)->toArray();
    //         $response = array_merge($overdue, $response);
    //     }

    //     return $response;
    // }

    public function calendar(Request $request)
    {
        $data = Arr::except($request->all(), ['_token']);

        $remindersByDate = CalendarUtil::getRemindersByDate2($data)->toArray();
        $remindersByDate2 = CalendarUtil::getRemindersByDate2_C($data);

        $response = CalendarUtil::transformRemindersData($remindersByDate2);

        if(CalendarUtil::isTodayIncluded($data))
        {
            $overdue = CalendarUtil::getOverdueCount($data)->toArray();
            $response = array_merge($overdue, $response);
        }

        $response = array_merge($remindersByDate, $response);
        return $response;
    }

    public function event(Request $request)
    {
        $data = Arr::except($request->all(), ['_token']);

        $res = ReminderUtil::saveSchedule($data);
        return true;
    }
}
