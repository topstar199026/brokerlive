<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;

use Response;

use App\Http\Controllers\Util\FormatUtil;
use App\Http\Controllers\Util\UserUtil;
use App\Http\Controllers\Util\DealUtil;
use App\Http\Controllers\Util\JournalUtil;
use App\Http\Controllers\Util\LoanSplitUtil;
use App\Http\Controllers\Util\PreferenceUtil;
use App\Http\Controllers\Util\CsvUtil;

use App\Datas\DashboardData;

class LeadController extends Controller
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

    protected $fields = array(
        'received_date' => 'Lead Received Date',
        'lead_name' => 'Lead Name',
        'contact_number' => 'Contact Number',
        'email' => 'Email',
        'notes' => 'Reminder / Last journal note',
        'referrer' => 'Referrer',
        'status_description' => 'Status',
    );

    /**
     * Show the application panel.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $resData = array();

        $data = Arr::except($request->all(), ['_token']);
        //$fromDate = Arr::exists($data, 'from') ? data_get($data, 'from') : date('m/d/Y', strtotime('-3 month'));
        $fromDate = Arr::exists($data, 'from') ? data_get($data, 'from') : date('m/d/Y');
        $toDate = Arr::exists($data, 'to') ? data_get($data, 'to') : date('m/d/Y');

        $fromDate = date('d M Y', strtotime($fromDate));
        $toDate = date('d M Y', strtotime($toDate));
        $resData['fromDate'] = $fromDate;
        $resData['toDate'] = $toDate;

        $resData['fields'] = $this->fields;

        $listReferer = array();

        $list = DealUtil::getDealContactByTypeId(3);
        if(!empty($list)) {
            foreach ($list as $dealContact) {
                $referrer = $dealContact->firstname . ' ' . $dealContact->lastname;
                $listReferer[$referrer] = array(
                    'name' => $referrer,
                    'value' => $dealContact->contact_id,
                    'active' => 0
                );
            }
        }
        if(!empty($listReferer)) {
            ksort($listReferer);
        }
        $resData['listReferer'] = $listReferer;

        return view('pages.lead.index', $resData);
    }

    public function leadDatatable(Request $request)
    {
        $data = Arr::except($request->all(), ['_token']);
        if(Arr::exists($data, 'fromdate') === true) { } else{
            $data = Arr::add($data, 'fromdate', date('d M Y'));
            $data =Arr::add($data, 'todate', date('d M Y'));
        }
        return $leads = DealUtil::getLeadsForTables($data);
    }

    public function csv(Request $request)
    {
        $data = Arr::except($request->all(), ['_token']);

        if(Arr::exists($data, 'fromdate') === true) { } else{
            $data = Arr::add($data, 'fromdate', date('d M Y'));
            $data =Arr::add($data, 'todate', date('d M Y'));
        }

        return CsvUtil::generateLeadCsv($data);
    }
}
