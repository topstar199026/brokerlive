<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;

use Response;

use App\Http\Controllers\Util\FormatUtil;
use App\Http\Controllers\Util\UserUtil;
use App\Http\Controllers\Util\DealUtil;
use App\Http\Controllers\Util\LenderUtil;
use App\Http\Controllers\Util\LoanSplitUtil;
use App\Http\Controllers\Util\WhiteboardUtil;
use App\Http\Controllers\Util\CsvUtil;

use App\Datas\DashboardData;

class WhiteboardController extends Controller
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

    /**
     * Show the application panel.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $resData['page_type'] = 'pipeline';

        $data = Arr::except($request->all(), ['_token']);
        $fromDate = Arr::exists($data, 'from') ? data_get($data, 'from') : date('m/d/Y', strtotime('-1 month'));
        $toDate = Arr::exists($data, 'to') ? data_get($data, 'to') : date('m/d/Y');

        $resData['fromDate'] = date('d M Y', strtotime($fromDate));
        $resData['toDate'] = date('d M Y', strtotime($toDate));

        $filter['fromDate'] = date('d M Y', strtotime($fromDate));
        $filter['toDate'] = date('d M Y', strtotime($toDate));

        $approvedSection = LoanSplitUtil::getApprovedWhiteboard($filter);
        $resData['approvedSection'] = $approvedSection;

        $pendingSection = LoanSplitUtil::getPendingWhiteboard($filter);
        $resData['pendingSection'] = $pendingSection;

        $aipSection = LoanSplitUtil::getAipWhiteboard($filter);
        $resData['aipSection'] = $aipSection;

        $submittedSection = LoanSplitUtil::getSubmittedWhiteboard($filter);
        $resData['submittedSection'] = $submittedSection;

        $committedSection = LoanSplitUtil::getCommittedWhiteboard($filter);
        $resData['committedSection'] = $committedSection;

        $hotSection = LoanSplitUtil::getHotWhiteboard($filter);
        $resData['hotSection'] = $hotSection;

        $settledSection = LoanSplitUtil::getSettledWhiteboard($filter);
        $resData['settledSection'] = $settledSection;

        return view('pages.whiteboard.index', $resData);
    }

    public function combined(Request $request)
    {
        $resData['page_type'] = 'combined';

        $data = Arr::except($request->all(), ['_token']);
        $fromDate = Arr::exists($data, 'from') ? data_get($data, 'from') : date('m/d/Y', strtotime('-1 month'));
        $toDate = Arr::exists($data, 'to') ? data_get($data, 'to') : date('m/d/Y');

        $resData['fromDate'] = date('d M Y', strtotime($fromDate));
        $resData['toDate'] = date('d M Y', strtotime($toDate));

        $filter['fromDate'] = date('d M Y', strtotime($fromDate));
        $filter['toDate'] = date('d M Y', strtotime($toDate));

        $approvedSection = LoanSplitUtil::getApprovedWhiteboard($filter, true);
        $resData['approvedSection'] = $approvedSection;

        $pendingSection = LoanSplitUtil::getPendingWhiteboard($filter, true);
        $resData['pendingSection'] = $pendingSection;

        $aipSection = LoanSplitUtil::getAipWhiteboard($filter, true);
        $resData['aipSection'] = $aipSection;

        $submittedSection = LoanSplitUtil::getSubmittedWhiteboard($filter, true);
        $resData['submittedSection'] = $submittedSection;

        $settledSection = LoanSplitUtil::getSettledWhiteboard($filter, true);
        $resData['settledSection'] = $settledSection;

        //return $resData;

        return view('pages.whiteboard.combined', $resData);
    }

    public function basic(Request $request)
    {
        $resData['page_type'] = 'basic';

        $data = Arr::except($request->all(), ['_token']);
        $fromDate = Arr::exists($data, 'from') ? data_get($data, 'from') : date('m/d/Y', strtotime('-10 years'));
        $toDate = Arr::exists($data, 'to') ? data_get($data, 'to') : date('m/d/Y');

        $resData['fromDate'] = date('d M Y', strtotime($fromDate));
        $resData['toDate'] = date('d M Y', strtotime($toDate));

        $filter['fromDate'] = date('d M Y', strtotime($fromDate));
        $filter['toDate'] = date('d M Y', strtotime($toDate));

        $rows = WhiteboardUtil::getWhiteboardRow($fromDate, $toDate);
        $resData['rows'] = $rows;
        //return $resData;

        return view('pages.whiteboard.basic', $resData);
    }

    public function business(Request $request)
    {
        $resData['page_type'] = 'business';

        $data = Arr::except($request->all(), ['_token']);
        $fromDate = Arr::exists($data, 'from') ? data_get($data, 'from') : date('m/d/Y', strtotime('-1 month'));
        $toDate = Arr::exists($data, 'to') ? data_get($data, 'to') : date('m/d/Y');

        $resData['fromDate'] = date('d M Y', strtotime($fromDate));
        $resData['toDate'] = date('d M Y', strtotime($toDate));

        $filter['fromDate'] = date('d M Y', strtotime($fromDate));
        $filter['toDate'] = date('d M Y', strtotime($toDate));

        $rows = LoanSplitUtil::getBusinessWhiteboard($filter, true);
        $resData['rows'] = $rows;
        //return $resData;

        return view('pages.whiteboard.business', $resData);
    }

    public function marketing(Request $request)
    {
        $resData['page_type'] = 'marketing';

        $data = Arr::except($request->all(), ['_token']);
        $fromDate = Arr::exists($data, 'from') ? data_get($data, 'from') : date('m/d/Y', strtotime('-1 month'));
        $toDate = Arr::exists($data, 'to') ? data_get($data, 'to') : date('m/d/Y');

        $resData['fromDate'] = date('d M Y', strtotime($fromDate));
        $resData['toDate'] = date('d M Y', strtotime($toDate));

        $filter['fromDate'] = date('d M Y', strtotime($fromDate));
        $filter['toDate'] = date('d M Y', strtotime($toDate));

        $lenders = LenderUtil::getLenders();
        $resData['lenders'] = $lenders;

        $dealStatus = DealUtil::getDealStatus();
        $resData['dealStatus'] = $dealStatus;

        $tags = array(
            "full app" => "Full App",
            "pre app" => "Pre App",
            "purchase" => "Purchase",
            "refi" => "Refi",
            "oo" => "O/O",
            "inv" => "Inv",
            "pi" => "P&I",
            "io" => "I/O",
            "fhb" => "FHB",
            "fhog" => "FHOG",
            "land" => "Land",
            "construction" => "Const.",
            "top up" => "Top Up",
            "maintenance" => "Maint."
        );
        $resData['tags'] = $tags;

        $listParams = ["lender", "lvr", "status"];
        foreach ($listParams as $param) {
            if(data_get($data, $param))
            {
                $filter[$param] = explode(',', data_get($data, $param));
            }
        }

        $resData['filter'] = $filter;

        $rows = DealUtil::getMarketingWhiteboard($filter);
        $resData['rows'] = $rows;

        return view('pages.whiteboard.marketing', $resData);
    }

    public function csv(Request $request)
    {
        $data = Arr::except($request->all(), ['_token']);
        $fromDate = Arr::exists($data, 'from') ? data_get($data, 'from') : date('m/d/Y', strtotime('-1 month'));
        $toDate = Arr::exists($data, 'to') ? data_get($data, 'to') : date('m/d/Y');

        $resData['fromDate'] = date('d M Y', strtotime($fromDate));
        $resData['toDate'] = date('d M Y', strtotime($toDate));

        $filter['fromDate'] = date('d M Y', strtotime($fromDate));
        $filter['toDate'] = date('d M Y', strtotime($toDate));

        switch (data_get($data, 'type'))
        {
            case 'pipeline':
                return CsvUtil::generatePipelineCsv(data_get($data, 'section'), $filter);
                break;
            case 'combined':
                return CsvUtil::generateCombinedCsv(data_get($data, 'section'), $filter);
                break;
            case 'basic':
                $fromDate = Arr::exists($data, 'from') ? data_get($data, 'from') : date('m/d/Y', strtotime('-10 years'));
                $toDate = Arr::exists($data, 'to') ? data_get($data, 'to') : date('m/d/Y');

                $resData['fromDate'] = date('d M Y', strtotime($fromDate));
                $resData['toDate'] = date('d M Y', strtotime($toDate));

                $filter['fromDate'] = date('d M Y', strtotime($fromDate));
                $filter['toDate'] = date('d M Y', strtotime($toDate));
                return CsvUtil::generateBasicCsv($filter);
                break;
            case 'business':
                return CsvUtil::generateBusinessCsv($filter);
                break;
            case 'marketing':
                $listParams = ['lender', 'lvr', 'status'];
                foreach ($listParams as $param) {
                    if(data_get($data, $param))
                    {
                        $filter[$param] = explode(',', data_get($data, $param));
                    }
                }
                return CsvUtil::generateMarketingCsv($filter);
                break;
            // case 'team':
            //     $this->csv_team();
            //     break;
        }
    }

}
