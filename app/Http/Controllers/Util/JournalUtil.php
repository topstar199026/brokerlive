<?php

namespace App\Http\Controllers\Util;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;

use DB;

use App\Models\JournalEntry;
use App\Models\JournalType;
use App\Models\ArchiveJournalEntry;

use App\Http\Controllers\Util\UserUtil;
use App\Http\Controllers\Util\CommonDbUtil;
use App\Http\Controllers\Util\ReminderUtil;

use App\Datas\JournalTemp;

class JournalUtil extends Controller
{
    public static function getJournalsByDealId($dealId, $limit = null, $page = null, $tags = null, $fromdate = null)
    {
        //return $dealId. $limit. $page .$tags .$fromdate;
        $_journals = self::buildIndexQuery();
        $_journals = self::filterQuery($_journals, $dealId, $tags, $fromdate);
        $_journals = self::limitQuery($_journals, $limit, $page);
        $_journals = $_journals->get();
        if(count($_journals)<1){
            $countJournals = JournalEntry::select(DB::raw('count(distinct journalentries.id) total'))
                ->orderBy('journalentries.entrydate', 'desc');
            $countJournals = self::filterQuery($countJournals, $dealId);
            $count = $countJournals->get()->count();
            $journals = self::buildIndexQuery('Archive');
            $journals = self::filterQuery($journals, $dealId, $tags, $fromdate,  'archive_journalentries');
            $journals = self::limitQuery($journals, $limit, $page, array('firstTotal' => $count));
            return $journals->get();
        }else{
            return $_journals;
        }

    }

    public static function buildIndexQuery($tablePrefix = '')
    {
        $journals = null;
        $tablePrefixLower = strtolower($tablePrefix);
        $tablePrefixTypes = $tablePrefixLower ? $tablePrefixLower.'_' : '';

        switch ($tablePrefix)
        {
            case '':
                $journals = JournalEntry::select(
                    DB::raw('GROUP_CONCAT(contenttags.name SEPARATOR ", ") AS typename'),
                    DB::raw('GROUP_CONCAT(contenttags.id SEPARATOR ",") AS typeid'),
                    DB::raw('GROUP_CONCAT('.$tablePrefixTypes.'journaltypes.type_id SEPARATOR ",") AS journaltypeid'),
                    $tablePrefixTypes.'journalentries.*'
                );
                break;
            case 'Archive':
                $journals = ArchiveJournalEntry::select(
                    DB::raw('GROUP_CONCAT(contenttags.name SEPARATOR ", ") AS typename'),
                    DB::raw('GROUP_CONCAT(contenttags.id SEPARATOR ",") AS typeid'),
                    DB::raw('GROUP_CONCAT('.$tablePrefixTypes.'journaltypes.type_id SEPARATOR ",") AS journaltypeid'),
                    $tablePrefixTypes.'journalentries.*'
                );
                break;
        }
        $journals = $journals
        ->leftJoin($tablePrefixTypes.'journaltypes', $tablePrefixTypes.'journaltypes.'.$tablePrefixTypes.'journal_id', '=', $tablePrefixTypes.'journalentries.id')
        ->leftJoin('contenttags',$tablePrefixTypes.'journaltypes.type_id', '=', 'contenttags.id')
        ->groupBy($tablePrefixTypes.'journalentries.id')
        ->orderBy($tablePrefixTypes.'journalentries.entrydate', 'desc');

        return $journals;
    }

    public static function filterQuery($journals, $dealId, $tags = null, $fromdate = null, $tableName = 'journalentries')
    {
        $journals->join('deals', 'deals.id', '=', $tableName.'.deal_id');

        $dealId &&
        $journals->where($tableName.'.deal_id', '=', $dealId);

        $tags &&
        $journals->where($tableName.'.tags', 'LIKE', '%'.$tags.'%');

        $fromdate &&
        $journals->where($tableName.'.entrydate', '>', strtotime($fromdate));

        return $journals;
    }

    public static function filterQuery2($journals, $data)
    {
        $fromDate = Arr::exists($data, 'fromdate') ? date('Y-m-d 00:00:00', strtotime(data_get($data, 'fromdate'))) : date('Y-m-d 00:00:00');
        $toDate = Arr::exists($data, 'todate') ? date('Y-m-d 23:59:59', strtotime(data_get($data, 'todate'))) : date('Y-m-d 23:59:59');

        $journals->where('entrydate', '>=', $fromDate);
        $journals->where('entrydate', '<=', $toDate);

        Arr::exists($data, 'user_id') &&
            $journals->where('user_id', '=', data_get($data, 'user_id'));

        Arr::exists($data, 'deal_id') &&
            $journals->where('deal_id', '=', data_get($data, 'deal_id'));

        Arr::exists($data, 'by') && data_get($data, 'by') != null &&
            $journals->whereIn('journalentries.user_id', explode(',', data_get($data, 'by')));

        return $journals;
    }

    public static function limitQuery($journals, $_limit, $_page, $pagination = array())
    {
        $defaultPagination = array('page' => 1, 'limit' => 10, 'firstTotal' => null);
        $pagination = array_merge($defaultPagination, $pagination);

        $limit = is_numeric($_limit) ? $_limit : $pagination['limit'];
        $page = is_numeric($_page) ? $_page : $pagination['page'];

        if ($pagination['firstTotal']) {
            $firstTotalPage = $pagination['firstTotal']%$limit ? (int)($pagination['firstTotal']/$limit) + 1 : $pagination['firstTotal']/$limit;
            $page = $page - $firstTotalPage;
        }

        $offset = ($page - 1) * $limit;

        $journals->skip($offset)->take($limit);
        return $journals;
    }

    public static function createJournal($reminder, $journalContent, $tags)
    {
        $journalId = self::saveJournalEntry($journalContent, $reminder->id, $reminder->deal_id);
        self::saveJournalTypes($journalId, $tags);
    }

    public static function saveJournalEntry($content, $reminderId = null, $dealId = null)
    {
        $userRole = Auth::user()->getUserRole();
        $newJournal = new JournalEntry;
        $newJournal->deal_id = $dealId;
        $newJournal->entrydate = date("Y-m-d H:i:s");
        $newJournal->notes = $content;
        $newJournal->reminder_id = $reminderId;
        $newJournal->user_id = Auth::id();
        $newJournal->username = Auth::user()->username;
        $newJournal->is_broker = $userRole->broker ? 'Y' : 'N';
        $newJournal->is_assistants = $userRole->personalAssistant ? 'Y' : 'N';
        $newJournal->save();
        return $newJournal->id;
    }

    public static function saveJournalTypes($journalId, $typeIds)
    {
        if(count($typeIds))
        {
            for($i = 0; $i < count($typeIds); $i++){
                $newJournalType = new JournalType;
                $newJournalType->journal_id = $journalId;
                $newJournalType->type_id = $typeIds[$i];
                $newJournalType->save();
            }
        }
    }

    public static function saveJournal($journalId, $action, $data)
    {
        $journalEntry = $journalId ? JournalEntry::find($journalId) : new JournalEntry;
        switch($action)
        {
            case 'new':
                $journalEntry->notes = data_get($data, 'notes', null);
                $journalEntry->entrydate = date('Y-m-d H:i:s');
                $journalEntry->deal_id = data_get($data, 'deal_id');
                $journalEntry->username = Auth::user()->username;
                $journalEntry->user_id = Auth::id();
                $journalEntry->created_by = Auth::id();
                break;
            case 'delete':
                break;
        }

        $journalEntry->is_broker = data_get($data, 'is_broker', null);
        $journalEntry->is_assistants = data_get($data, 'is_assistants', null);
        $journalEntry->is_others = data_get($data, 'is_others', null);

        $journalEntry->save();

        if($journalId)
        {
            JournalType::where('journal_id', '=', $journalId)->delete();
        }

        if(Arr::exists($data, 'typeid'))
        {
            self::saveJournalType($journalEntry->id, data_get($data, 'typeid'));
        }

        return $journalEntry;
    }

    public static function saveJournalType($journalId, $journalTypes)
    {
        if (!is_array($journalTypes)) {
            $journalTypes = explode(',', $journalTypes);
        }
        foreach ($journalTypes as $typeid) {
            $newJournalType = new JournalType;
            $newJournalType->journal_id = $journalId;
            $newJournalType->type_id = $typeid;
            $newJournalType->save();
        }
        return ;
    }

    public static function addJournalEntry($reminder, $_reminder, $data, $action) {
        $details = $reminder->details;

        $comments = data_get($data, 'comments', null);
        $duedate = data_get($data, 'duedate', null);
        $who_for =  data_get($data, 'who_for', array());
        $tags =  data_get($data, 'tags', array());
        $tags = ReminderUtil::convertTagNameToId($tags);

        $journalContent = "";

        if ($_reminder !== null) {
            $journalContent = sprintf(
                JournalTemp::$ReminderCreated,
                $_reminder->user->username ?? '',
                $_reminder->getCreateDate(),
                $_reminder->getDueDate(),
                $_reminder->who_for
            );

            $journalContent .= '<br>';
        } else {
            $journalContent = "";
        }

        switch ($action) {
            case 'complete':
                $journalContent .= sprintf(
                    JournalTemp::$ReminderCompleted,
                    Auth::user()->username,
                    date("d M Y, h:i A"),
                    implode(', ', $who_for),
                    implode(', ',ReminderUtil::convertTagNameToId($tags)),
                    $details,
                    $comments
                );
                break;
            case 'repeat':
                $journalContent .= sprintf(
                    JournalTemp::$ReminderRepeated,
                    Auth::user()->username,
                    date("d M Y, h:i A"),
                    $duedate,
                    implode(', ', $who_for),
                    isset($_reminder) ? $_reminder->details : $details,
                    $comments
                );
                break;
            case 'change':
                $tags =  $reminder->tags;
                $tags = ReminderUtil::convertTagNameToId(explode(",",$tags));
                $journalContent .= sprintf(
                    JournalTemp::$ReminderChanged,
                    date("d M Y, h:i A"),
                    Auth::user()->username
                );
                break;
        }

        if (!empty($journalContent)) {
            self::createJournal($reminder, $journalContent, $tags);
        }
    }

    public static function getJournalsForTables($data)
    {
        $journals = JournalEntry::select(
                'deals.name as deal_name', 'dealstatuses.description as status_description', 'journalentries.*',
                DB::raw('DATE_FORMAT(journalentries.entrydate, "%d %M %Y, %h:%m %p") as _entrydate')
            )
            ->join('deals', 'deals.id', '=', 'journalentries.deal_id')
            ->join('dealstatuses', 'deals.status', '=', 'dealstatuses.id')
            ->whereIn('deals.user_id', UserUtil::getBrockerIds());
        $journals = self::filterQuery2($journals, $data);
        $columns = array('journalentries.entrydate', 'journalentries.username', 'deals.name', 'dealstatuses.description');
        $journalTable =  CommonDbUtil::getDataTable($journals, $data, $columns);

        // $journalTable = array(
        //     'status' => 'success',
        //     'error' => null,
        //     'data' => $journals,
        //     'recordsFiltered' => 0,
        //     'recordsTotal' => 0
        // );
        return $journalTable;
    }

    public static function getJournalsForCSV($data)
    {
        $journals = JournalEntry::select(
                'deals.name as deal_name', 'dealstatuses.description as status_description', 'journalentries.*',
                DB::raw('DATE_FORMAT(journalentries.entrydate, "%d %M %Y, %h:%m %p") as _entrydate')
            )
            ->join('deals', 'deals.id', '=', 'journalentries.deal_id')
            ->join('dealstatuses', 'deals.status', '=', 'dealstatuses.id')
            ->whereIn('deals.user_id', UserUtil::getBrockerIds());
        $journals = self::filterQuery2($journals, $data);

        return $journals->get();
    }


    public static function getCallCount($dateFrom, $dateTo)
    {
        return JournalEntry::join('journaltypes', 'journalentries.id', '=', 'journaltypes.journal_id')
            ->whereIn('journalentries.user_id', UserUtil::getBrockerIds())
            ->where('journaltypes.type_id', '=', '5')
            ->where('journalentries.created_at', '>=', $dateFrom)
            ->where('journalentries.created_at', '<', $dateTo)
            ->get()
            ->count();
    }

    public static function getAppointmentCount($dateFrom, $dateTo)
    {
        return JournalEntry::join('journaltypes', 'journalentries.id', '=', 'journaltypes.journal_id')
            ->whereIn('journalentries.user_id', UserUtil::getBrockerIds())
            ->where('journaltypes.type_id', '=', '6')
            ->where('journalentries.created_at', '>=', $dateFrom)
            ->where('journalentries.created_at', '<', $dateTo)
            ->get()
            ->count();
    }
}
