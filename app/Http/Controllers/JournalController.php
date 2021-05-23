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
use App\Http\Controllers\Util\CsvUtil;

class JournalController extends Controller
{
    public function index(Request $request)
    {
        $data = Arr::except($request->all(), ['_token']);

        $resData = array();

        $teams = UserUtil::getTeams();
        $resData['teams'] = $teams;

        $fromDate = date('d M Y', strtotime('-5 days'));
        $toDate = date('d M Y');
        $resData['fromDate'] = $fromDate;
        $resData['toDate'] = $toDate;

        $fields = array(
            'entrydate' => 'Entry Date',
            'user.username' => 'Actioned By',
            'deal.name' => 'Deal',
            'deal.status_description' => 'Deal Status',
            'notes' => 'Journal Entry',
        );
        $resData['fields'] = $fields;

        return view('pages.journal.index', $resData);
    }

    public function journalEntry(Request $request)
    {
        $dealId = $request->input('deal_id');
        $limit = $request->input('limit');
        $page = $request->input('page');
        $tags = $request->input('tags');
        $fromdate = $request->input('fromdate');

        $journals = JournalUtil::getJournalsByDealId($dealId, $limit, $page, $tags, $fromdate);
        $availableTags = DealUtil::getContentTags();
        return response()->json([
            'entries' => $journals,
            'availableTypes' => $availableTags,
        ]);
    }

    public function createJournalEntry(Request $request)
    {
        $id = $request->input('journal_id');
        $data = Arr::except($request->all(), ['_token']);
        $journal = JournalUtil::saveJournal(null, 'new', $data);
        return $journal ? 'success' : 'fail';
    }

    public function journalDatatable(Request $request)
    {
        $data = Arr::except($request->all(), ['_token']);
        return $journals = JournalUtil::getJournalsForTables($data);
    }

    public function csv(Request $request)
    {
        $data = Arr::except($request->all(), ['_token']);

        if(Arr::exists($data, 'fromdate') === true) { } else{
            $data = Arr::add($data, 'fromdate', date('d M Y'));
            $data =Arr::add($data, 'todate', date('d M Y'));
        }

        return CsvUtil::generateJournalCsv($data);
    }
}
