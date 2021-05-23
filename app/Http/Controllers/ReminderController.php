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
use App\Http\Controllers\Util\LenderUtil;
use App\Http\Controllers\Util\PreferenceUtil;

use App\Datas\DealAttribute;

class ReminderController extends Controller
{
    public function index(Request $request)
    {
        $data = Arr::except($request->all(), ['_token']);

        $resData = array();

        $reminders = ReminderUtil::getRemindersByCondition($data);
        $resData['reminders'] = $reminders;

        $reminderCount = $reminders->count();
        $resData['reminderCount'] = $reminderCount;

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

        return view('pages.reminder.index', $resData);
    }

    public function reminder(Request $request)
    {
        $data = Arr::except($request->all(), ['_token']);

        $reminders = ReminderUtil::getRemindersByDealId($data);
        $lenders = LenderUtil::getLenders();
        $availableTags = DealUtil::getContentTags();
        $defaulTime = PreferenceUtil::getDefaultTime();

        return response()->json([
            'entries' => $reminders,
            'availableTags' => $availableTags,
            'lenders' => $lenders,
            'defaulTime' => $defaulTime
        ]);
    }

    public function createReminder(Request $request)
    {
        $data = Arr::except($request->all(), ['_token']);
        $reminder = ReminderUtil::saveReminder(null, 'new', $data);
        return $reminder;
    }

    public function completeReminder(Request $request)
    {
        $id = $request->input('id');
        $data = Arr::except($request->all(), ['_token', 'id']);
        $reminder = ReminderUtil::saveReminder($id, 'complete', $data);
        return $id;
    }

    public function repeatReminder(Request $request)
    {
        $id = $request->input('id');
        $data = Arr::except($request->all(), ['_token', 'id']);
        return $reminder = ReminderUtil::saveReminder($id, 'repeat', $data);
        //$reminder = ReminderUtil::saveReminder(null, 'new', $data);
        return $id;
    }

    public function deleteReminder(Request $request, $id)
    {
        $reminder = ReminderUtil::saveReminder($id, 'delete', null);
        return $reminder;
    }

    public function reminderDatatable(Request $request)
    {
        $data = Arr::except($request->all(), ['_token']);
        return $reminders = ReminderUtil::getRemindersForTables($data);
    }

    public function reminderDatalist(Request $request)
    {
        $data = Arr::except($request->all(), ['_token']);
        return $reminders = ReminderUtil::getRemindersForLists($data);
    }

    public function form(Request $request)
    {
        $reminderId = $request->route('id');
        $reminder = ReminderUtil::getReminderById($reminderId);
        $tags = DealUtil::getContentTags();
        $valuesLender = LenderUtil::getLendersForSelect();
        return view('pages.reminder.form')
            ->with('reminder', $reminder)
            ->with('valuesLender', $valuesLender)
            ->with('tags', $tags)
            ->render();
    }
}
