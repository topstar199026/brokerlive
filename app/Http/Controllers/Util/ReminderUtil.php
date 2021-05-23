<?php

namespace App\Http\Controllers\Util;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

use DB;

use App\Models\Reminder;
use App\Models\ContactType;
use App\Models\ContentTag;

use App\Http\Controllers\Util\FormatUtil;
use App\Http\Controllers\Util\CommonDbUtil;
use App\Http\Controllers\Util\JournalUtil;


use App\Datas\JournalTemp;

class ReminderUtil extends Controller
{
    public static function mainQuery(){
        return Reminder::select(
                'deals.name AS deal_name', 'dealstatuses.description AS deal_status', 'reminders.*',
                DB::raw('DATE_FORMAT(reminders.duedate,"%d %M %Y") as _duedate'),
                DB::raw('DATE_FORMAT(reminders.startTime,"%l:%i %p") as _startTime')
            )
            ->join('deals', 'deals.id', '=', 'reminders.deal_id')
            ->join('dealstatuses', 'deals.status', '=', 'dealstatuses.id')
            ->where(function($query) {
                $query->whereNull('reminders.completed')
                      ->orWhere('reminders.completed', '=', 0);
            })
            ->whereIn('deals.user_id',UserUtil::getBrockerIds());
    }

    public static function mainQueryForCalendarList(){
        $_NOW = date('Y-m-d 00:00:00');

        return Reminder::select(
                'deals.name AS deal_name', 'dealstatuses.description AS deal_status', 'reminders.*',
                DB::raw('DATE_FORMAT(reminders.duedate,"%d %M %Y") as _duedate'),
                DB::raw('DATE_FORMAT(reminders.startTime,"%l:%i %p") as _startTime')
            )
            ->join('deals', 'deals.id', '=', 'reminders.deal_id')
            ->join('dealstatuses', 'deals.status', '=', 'dealstatuses.id')
            ->where(function($query) {
                $query->whereNull('reminders.completed')
                      ->orWhere('reminders.completed', '=', 0);
            })
            ->where(function($query) use($_NOW) {
                $query->whereNull('reminders.duedate')
                      ->orWhere('reminders.duedate', '<', $_NOW);
            })
            ->whereIn('deals.user_id',UserUtil::getBrockerIds());
    }

    public static function filterQuery($reminders,  $data)
    {
        data_get($data, 'deal_id', null) &&
            $reminders->where('deal_id', '=', data_get($data, 'deal_id'));

        $for = data_get($data, 'for', null);
        $for &&
            $reminders->where(function($query) use ($for) {
                $_tags = explode(',', $for);
                foreach ($_tags as $tag) {
                    $query->orWhere(DB::raw('FIND_IN_SET(\''.$tag.'\', `reminders`.`who_for`)'), '>', '0');
                }
            });

        $todate = data_get($data, 'todate', null);
        $todate &&
            $reminders->where(function ($query) use ($todate) {
                $query->where('reminders.duedate', '<=', date('Y-m-d 00:00:00', strtotime($todate)))
                    ->orWhereNull('reminders.duedate');
            });

        $tags = data_get($data, 'tags', null);
        $tags &&
            $reminders->where(function($query) use ($tags) {
                $_tags = explode(',', $tags);
                foreach ($_tags as $tag) {
                    $query->orWhere(DB::raw('FIND_IN_SET(\''.$tag.'\', `reminders`.`tags`)'), '>', '0');
                }
            });

        $status = data_get($data, 'status', null);
        $status &&
            $reminders->whereIn('deals.status',explode(',', $status));

        $lender = data_get($data, 'lender', null);
        $lender &&
            $reminders->join('loansplits', 'deals.id', '=', 'loansplits.deal_id')
                ->whereIn('loansplits.lender_id', explode(',', $lender));

        return $reminders;
    }

    public static function getRemindersByDealId($data)
    {
        $reminders = self::mainQuery();
        $reminders = self::filterQuery($reminders, $data);
        $reminders = $reminders->orderBy('reminders.duedate');

        return $reminders->get();
    }

    public static function getReminderById($id)
    {
        return Reminder::find($id);
    }



    public static function saveReminder($id, $action, $data)
    {
        $reminder = $id ? Reminder::find($id) : new Reminder;

        $_reminder = null;

        switch($action)
        {
            case 'new':
                $reminder->created_by = Auth::id();
                break;
            case 'complete':
                $_reminder = $reminder;
                $reminder->completed = 1;
                break;
            case 'repeat':
                $_reminder = $reminder;
                $reminder->completed = 1;
                $reminder->save();
                $reminder = new Reminder;
                $reminder->created_by = Auth::id();
                break;
            case 'delete':
               $journalContent = sprintf(
                    JournalTemp::$ReminderCreated,
                    $reminder->user->username ?? '',
                    $reminder->getCreateDate(),
                    $reminder->getDueDate(),
                    $reminder->who_for
                );
                $journalContent .= '<br>';
                $journalContent .= sprintf(
                    JournalTemp::$ReminderDeleted,
                    $reminder->user->username ?? '',
                    date("d M Y, h:i A"),
                    $reminder->details
                );
                JournalUtil::createJournal($reminder, $journalContent, self::convertTagNameToId(explode(",",$reminder->tags)));
                $reminder->delete();
                return $reminder;
                break;
        }

        if(Arr::exists($data, 'tags'))
        {
            data_set($data, 'tags', self::convertTagIdToName(data_get($data, 'tags')));
            $reminder->tags = implode(',', data_get($data, 'tags'));
        }
        if(Arr::exists($data, 'who_for'))
        {
            $reminder->who_for = implode(',', data_get($data, 'who_for'));
        }

        $reminder->duedate = FormatUtil::formatDateTime(data_get($data, 'duedate'));
        $reminder->details = data_get($data, 'details');
        $reminder->deal_id = data_get($data, 'deal_id');

        $reminder->starttime = FormatUtil::formatStartTime(data_get($data, 'starttime', null));
        $reminder->timelength = data_get($data, 'starttime', null) ? FormatUtil::getDuration(data_get($data, 'starttime', null), data_get($data, 'timelength', null)) : null;
        $reminder->lender_id = data_get($data, 'lender_id', null);

        $reminder->save();

        JournalUtil::addJournalEntry($reminder, $_reminder, $data, $action);

        return $reminder;
    }

    private static function convertTagIdToName($tagIds) {
        $availableTags = ContentTag::get();
        $map = array();
        foreach ($availableTags as $tag) {
            $map[$tag->id] = $tag->name;
        }

        $tagsArray = array();

        for ($i = 0; $i < count($tagIds); $i++) {
            if (isset($map[$tagIds[$i]])) {
                $tagsArray[] = $map[$tagIds[$i]];
            } else {
                $tagsArray[] = $tagIds[$i];
            }
        }

        return $tagsArray;
    }

    public static function convertTagNameToId($tagNames) {
        $availableTags = ContentTag::get();
        $map = array();
        foreach ($availableTags as $tag) {
            $map[$tag->name] = $tag->id;
        }

        $tagsArray = array();

        for ($i = 0; $i < count($tagNames); $i++) {
            if (isset($map[$tagNames[$i]])) {
                $tagsArray[] = $map[$tagNames[$i]];
            }
        }

        return $tagsArray;
    }

    public static function getRemindersByCondition($data)
    {
        $perPage = Arr::exists($data, 'page') ? 10 : 10;
        $pageNum = Arr::exists($data, 'page') ? data_get($data, 'page') : 1;

        $offset   = ($pageNum - 1) * $perPage;

        $fromDate = Arr::exists($data, 'fromDate') ? data_get($data, 'fromDate') : date('m/d/Y');
        $toDate = Arr::exists($data, 'toDate') ? data_get($data, 'toDate') : date('m/d/Y');
        $fromDate = date('Y-m-d 00:00:00', strtotime($fromDate));
        $toDate = date('Y-m-d 00:00:00', strtotime($toDate));

        return Reminder::select('deals.name', 'dealstatuses.description', 'reminders.*')
            ->join('deals', 'deals.id', '=', 'reminders.deal_id')
            ->join('dealstatuses', 'deals.status', '=', 'dealstatuses.id')
            ->whereNull('reminders.completed')
            ->orWhere('reminders.completed', '=', 0)
            ->orderBy('reminders.duedate', 'ASC')
            ->orderBy('deals.name', 'ASC')
            ->skip($offset)
            ->take($perPage)
            ->get();
    }

    public static function getRemindersForTables($data)
    {
        $reminders = self::mainQuery();
        $reminders = self::filterQuery($reminders, $data);
        $columns = array('reminders.duedate', 'deals.name', 'dealstatuses.description', 'reminders.details', 'reminders.who_for');
        $reminderTable =  CommonDbUtil::getDataTable($reminders, $data, $columns);
        return $reminderTable;
    }

    public static function getRemindersForLists($data)
    {
        $reminders = self::mainQueryForCalendarList();
        $reminders = self::filterQuery($reminders, $data);
        // $columns = array('reminders.duedate', 'deals.name', 'dealstatuses.description', 'reminders.details', 'reminders.who_for');
        // $reminderTable =  CommonDbUtil::getDataTable($reminders, $data, $columns);
        return $reminders->get()->toArray();
    }

    public static function saveSchedule($data)
    {
        $id = data_get($data, 'id');
        $allDay = data_get($data, 'allDay');
        $duedate = data_get($data, 'duedate');
        $starttime = data_get($data, 'starttime');
        $length = data_get($data, 'length', null);

        if($allDay == 'true') {$length = null;};


        $reminder = Reminder::find($id);

        $reminder->duedate = FormatUtil::formatDateTime($duedate);
        $reminder->starttime = FormatUtil::formatStartTime($starttime);
        $reminder->timelength = $length;
        $reminder->save();
        JournalUtil::addJournalEntry($reminder, null, $data, 'change');
        return true;

    }
}
