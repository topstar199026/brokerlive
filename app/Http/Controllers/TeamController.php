<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;

use Response;

use App\Http\Controllers\Util\TeamUtil;
use App\Http\Controllers\Util\CsvUtil;

use App\Datas\SelectData;

class TeamController extends Controller
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
        $resData['page_type'] = 'team';

        $data = Arr::except($request->all(), ['_token']);

        $filter = TeamUtil::buildFilter($data);

        if(!Arr::exists($data, 'from'))
        {
            $filter->fromDate = date('m/d/Y', strtotime('-10 years'));
            $filter->toDate = date('m/d/Y');
        }

        $fromDate = Arr::exists($data, 'from') ? data_get($data, 'from') : date('m/d/Y', strtotime('-10 years'));
        $toDate = Arr::exists($data, 'to') ? data_get($data, 'to') : date('m/d/Y');

        // $resData['fromDate'] = date('d M Y', strtotime($fromDate));
        // $resData['toDate'] = date('d M Y', strtotime($toDate));

        // $filter['fromDate'] = date('d M Y', strtotime($fromDate));
        // $filter['toDate'] = date('d M Y', strtotime($toDate));

        $resData['filter'] = $filter;

        $select = TeamUtil::getTeamSelectFilter($filter);
        $resData['select'] = $select;
        //return $filter->get_team_user_list();
        $reportData = TeamUtil::buildReportTeamData($filter);
        $resData['reportData'] = $reportData;

        return view('pages.team.index', $resData);
    }

    public function broker(Request $request)
    {
        $resData['page_type'] = 'broker';

        $data = Arr::except($request->all(), ['_token']);

        $filter = TeamUtil::buildFilter($data);
        //return $filter->team_brokers();

        if(!Arr::exists($data, 'from'))
        {
            $filter->fromDate = date('m/d/Y', strtotime('-10 years'));
            $filter->toDate = date('m/d/Y');
        }

        $fromDate = Arr::exists($data, 'from') ? data_get($data, 'from') : date('m/d/Y', strtotime('-10 years'));
        $toDate = Arr::exists($data, 'to') ? data_get($data, 'to') : date('m/d/Y');

        $resData['filter'] = $filter;

        $select = TeamUtil::getTeamSelectFilter($filter);
        $resData['select'] = $select;
        //return $filter->get_team_user_list();
        $rows = TeamUtil::buildReportBrokerData($filter);
        $resData['rows'] = $rows;

        return view('pages.team.broker', $resData);
    }

    public function pipeline(Request $request)
    {
        $resData['page_type'] = 'pipeline';

        $data = Arr::except($request->all(), ['_token']);

        $filter = TeamUtil::buildFilter($data);

        $fromDate = Arr::exists($data, 'from') ? data_get($data, 'from') : date('m/d/Y', strtotime('-10 years'));
        $toDate = Arr::exists($data, 'to') ? data_get($data, 'to') : date('m/d/Y');

        $resData['filter'] = $filter;

        $select = TeamUtil::getTeamSelectFilter($filter);
        $resData['select'] = $select;

        $approvedSection = TeamUtil::getApprovedTeam($filter);
        $resData['approvedSection'] = $approvedSection;

        $pendingSection = TeamUtil::getPendingTeam($filter);
        $resData['pendingSection'] = $pendingSection;

        $aipSection = TeamUtil::getAipTeam($filter);
        $resData['aipSection'] = $aipSection;

        $submittedSection = TeamUtil::getSubmittedTeam($filter);
        $resData['submittedSection'] = $submittedSection;

        $committedSection = TeamUtil::getCommittedTeam($filter);
        $resData['committedSection'] = $committedSection;

        $hotSection = TeamUtil::getHotTeam($filter);
        $resData['hotSection'] = $hotSection;

        $settledSection = TeamUtil::getSettledTeam($filter);
        $resData['settledSection'] = $settledSection;

        return view('pages.team.pipeline', $resData);
    }

    public function combined(Request $request)
    {
        $resData['page_type'] = 'combined';

        $data = Arr::except($request->all(), ['_token']);

        $filter = TeamUtil::buildFilter($data);

        $fromDate = Arr::exists($data, 'from') ? data_get($data, 'from') : date('m/d/Y', strtotime('-10 years'));
        $toDate = Arr::exists($data, 'to') ? data_get($data, 'to') : date('m/d/Y');

        $resData['filter'] = $filter;

        $select = TeamUtil::getTeamSelectFilter($filter);
        $resData['select'] = $select;

        $approvedSection = TeamUtil::getApprovedTeam($filter, true);
        $resData['approvedSection'] = $approvedSection;

        $pendingSection = TeamUtil::getPendingTeam($filter, true);
        $resData['pendingSection'] = $pendingSection;

        $aipSection = TeamUtil::getAipTeam($filter, true);
        $resData['aipSection'] = $aipSection;

        $submittedSection = TeamUtil::getSubmittedTeam($filter, true);
        $resData['submittedSection'] = $submittedSection;

        $settledSection = TeamUtil::getSettledTeam($filter, true);
        $resData['settledSection'] = $settledSection;

        return view('pages.team.combined', $resData);
    }

    public function basic(Request $request)
    {
        $resData['page_type'] = 'basic';

        $data = Arr::except($request->all(), ['_token']);

        $filter = TeamUtil::buildFilter($data);

        if(!Arr::exists($data, 'from'))
        {
            $filter->fromDate = date('m/d/Y', strtotime('-10 years'));
            $filter->toDate = date('m/d/Y');
        }

        $fromDate = Arr::exists($data, 'from') ? data_get($data, 'from') : date('m/d/Y', strtotime('-10 years'));
        $toDate = Arr::exists($data, 'to') ? data_get($data, 'to') : date('m/d/Y');

        $resData['filter'] = $filter;

        $select = TeamUtil::getTeamSelectFilter($filter);
        $resData['select'] = $select;
        //return $filter->get_team_user_list();
        $rows = TeamUtil::buildReportBasicData($filter);
        $resData['rows'] = $rows;

        return view('pages.team.basic', $resData);
    }

    public function csv(Request $request)
    {
        $data = Arr::except($request->all(), ['_token']);

        $filter = TeamUtil::buildFilter($data);

        if(!Arr::exists($data, 'from'))
        {
            $filter->fromDate = date('m/d/Y', strtotime('-10 years'));
            $filter->toDate = date('m/d/Y');
        }

        $fromDate = Arr::exists($data, 'from') ? data_get($data, 'from') : date('m/d/Y', strtotime('-10 years'));
        $toDate = Arr::exists($data, 'to') ? data_get($data, 'to') : date('m/d/Y');

        switch (data_get($data, 'type'))
        {
            case 'team':
                return CsvUtil::generateTeamIndexCsv($filter);
                break;
            case 'broker':
                return CsvUtil::generateTeamBrokerCsv($filter);
                break;
            case 'pipeline':
                return CsvUtil::generateTeamPipelineCsv(data_get($data, 'section'), $filter);
                break;
            case 'combined':
                return CsvUtil::generateTeamCombinedCsv(data_get($data, 'section'), $filter);
                break;
            case 'basic':
                return CsvUtil::generateTeamBasicCsv($filter);
                break;
        }
    }
}
