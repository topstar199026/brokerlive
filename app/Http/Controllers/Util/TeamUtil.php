<?php

namespace App\Http\Controllers\Util;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;
use DB;
use Response;

use App\Models\User;
use App\Models\Deal;
use App\Models\Contact;
use App\Models\Dealstatus;
use App\Models\DealNotify;
use App\Models\DealContact;
use App\Models\ContactType;
use App\Models\ContentTag;
use App\Models\Role;
use App\Models\FileManagement;
use App\Models\PersonTitle;
use App\Models\LoanSplit;
use App\Models\LoanApplicant;
use App\Models\DocumentStatus;
use App\Models\Lender;
use App\Models\CacheTeamBasic;

use App\Http\Controllers\Util\FormatUtil;
use App\Http\Controllers\Util\UserUtil;
use App\Http\Controllers\Util\RelationUtil;
use App\Http\Controllers\Util\WhiteboardUtil;
use App\Http\Controllers\Util\OrganisationUtil;
use App\Http\Controllers\Util\TeamPipelineUtil;
use App\Http\Controllers\Util\TeamStatsUtil;

use App\Datas\SelectData;
use App\Datas\SelectOptionData;
use App\Datas\SelectOptionGroupData;
use App\Datas\ReportFilterTeam;
use App\Datas\ReportTeamData;
use App\Datas\ReportTeamSection;
use App\Datas\ReportTeamRow;

class TeamUtil extends Controller
{
    public static function buildFilter($data)
    {
        $filter = new ReportFilterTeam(Auth::user());

        $filter->fromDate = Arr::exists($data, 'from') ? data_get($data, 'from') : date('m/d/Y', strtotime('-1 years'));
        $filter->toDate = Arr::exists($data, 'to') ? data_get($data, 'to') : date('m/d/Y');

        if (Arr::exists($data, 'team'))
        {
            $filter_teams = array();
            $teams = explode(',', data_get($data, 'team'));
            foreach ($teams as $team) {
                $temp = explode('#', $team);
                $filter_teams[] = array(
                    'type' => $temp[0],
                    'value' => $temp[1],
                );
            }
            $filter->teams = $filter_teams;
        }
        $filter->fromDate = date('d M Y', strtotime($filter->fromDate));
        $filter->toDate = date('d M Y', strtotime($filter->toDate));
        return $filter;
    }

    public static function getTeamSelectFilter($filter)
    {
        $select = new SelectData();
        $select->id = 'team-filter-for';
        $select->class = 'multiselect';

        if(Auth::user()->isHeadBroker())
        {
            $group_teams = new SelectOptionGroupData('Teams');
            $group_teams->add_option('My Brokers', 'HeadBroker#headbroker');
            $select->add_optiongroup($group_teams);
        }

        $group_orgs = new SelectOptionGroupData('Organisations');

        $related_orgs = RelationUtil::getTeamRelatedOrg();

        if ($related_orgs == null)
        {
            return $select;
        }

        $organisation = OrganisationUtil::getOrganisationById($related_orgs->relation_id);
        $orgTree = $organisation->tree();
        foreach ($orgTree as $org) {
            if (in_array($org['id'], $filter->organisation_ids())) {
                $group_orgs->add_option($org['legal_name'], 'Organisation#' . $org['id'], true);
            } else {
                $group_orgs->add_option($org['legal_name'], 'Organisation#' . $org['id']);
            }
        }
        $select->add_optiongroup($group_orgs);


        return $select;
    }

    public static function queryTeam($filter)
    {
        //return CacheTeamBasic::whereIn('user_id', implode(',', $filter->team_brokers()))
        return CacheTeamBasic::whereIn('user_id', $filter->team_brokers())
            ->where('record_date', '>=', date('Y-m-d', strtotime($filter->fromDate)))
            ->where('record_date', '<=', date('Y-m-d', strtotime($filter->toDate)))
            ->groupBy('broker_name')
            ->groupBy('year')
            ->groupBy('month')
            ->orderBy('record_date', 'DESC')
            ->orderBy('broker_name', 'DESC')
            ->get();
    }

    public static function queryBroker($filter)
    {
        return User::join('deals', 'users.id', '=', 'deals.user_id')
            ->whereIn('users.id', $filter->team_brokers())
            ->groupBy('users.id')
            ->select('users.lastname', 'users.firstname', DB::raw('COUNT(users.id) AS deals'))
            ->get();
    }

    public static function queryBasic($filter)
    {
        return CacheTeamBasic::whereIn('user_id', $filter->team_brokers())
            ->where('record_date', '>=', date('Y-m-d', strtotime($filter->fromDate)))
            ->where('record_date', '<=', date('Y-m-d', strtotime($filter->toDate)))
            ->groupBy('broker_name')
            ->groupBy('year')
            ->groupBy('month')
            ->orderBy('record_date', 'DESC')
            ->select('year', 'month',
                DB::raw('SUM(Leads) AS Leads'),
                DB::raw('SUM(Calls) AS Calls'),
                DB::raw('SUM(Appts) AS Appts'),
                DB::raw('SUM(Splits) AS Splits'),
                DB::raw('SUM(Submissions) AS Submissions'),
                DB::raw('SUM(Submissions) AS SubmissionsNumber'),
                DB::raw('SUM(Preapp) AS Preapp'),
                DB::raw('SUM(Pending) AS Pending'),
                DB::raw('SUM(Fullapp) AS Fullapp'),
                DB::raw('SUM(Settled) AS Settled')
            )
            ->get();
    }

    public static function convertSplitsToTeams($splits, $actualcol)
    {
        $teamSection = New ReportTeamSection($actualcol);

        foreach($splits as $split)
        {
            $referrer = $split->referrer;
            if ($referrer) {}
            else
            {
                $referrer = $split->deal->referrer();
            }

            $team = new ReportTeamRow();

            $team->deal_id = $split->deal->id;
            $team->deal_status = $actualcol;
            $team->broker_code = '';
            $team->broker = substr($split->deal->broker->firstname, 0, 1) . substr($split->deal->broker->lastname, 0, 1);
            $team->borrower = $split->deal->user_id == Auth::id() ? $split->deal->name : "Confidential";
            $team->settlement_date = FormatUtil::formatDateTime3($split->settlementdate);

            $team->referrer = '';
            if ($referrer != null)
            {
                try {

                    $team->referrer = $referrer->lastname . ', ' . $referrer->firstname;

                } catch (\Exception $e) {

                    $team->referrer = '';
                }
            }

            $team->lender_id = '';
            $team->lender = $split->lender;
            $team->loan_amount = $split->subloan;

            switch($actualcol)
            {
                case 'aip':
                    $team->actual = $split->aipvalue;
                    $team->month = date("M",strtotime($split->aip));
                    break;
                case 'conditional':
                    $team->actual = $split->conditionalvalue;
                    $team->month = date("M",strtotime($split->conditional));
                    break;
                case 'approved':
                    $team->actual = $split->approvedvalue;
                    $team->month = date("M",strtotime($split->approved));
                    break;
                case 'settled':
                    $team->actual = $split->settledvalue;
                    $team->month = date("M",strtotime($split->settled));
                    break;
                case 'submitted':
                    $team->actual = $split->submittedvalue;
                    $team->month = date("M",strtotime($split->submitted));
                    break;
            }

            // $team->doc_status = $split->documentstatus->name;
            $team->finance_due = FormatUtil::formatDateTime3($split->financeduedate);
            $team->submitted_date = FormatUtil::formatDateTime3($split->submitted);
            $team->aip = FormatUtil::formatDateTime3($split->aip);
            $team->conditional = FormatUtil::formatDateTime3($split->conditional);
            $team->full_approval = FormatUtil::formatDateTime3($split->approved);

            $teamSection->add_row($team);
        }

        return $teamSection;
    }

    public static function buildReportTeamData($filter)
    {
        $rows = self::queryTeam($filter);
        $data = array();
        $dataYm = array();

        foreach($rows as $row){
            $time= $row['month'] . "-" . $row['year'];
            unset($row['year']);
            unset($row['month']);

            if (!isset($data[$row['broker_name']])) {
                $data[$row['broker_name']] = new ReportTeamData();
            }
            $data[$row['broker_name']]->add_row($row);

            if (!isset($dataYm[$time][$row['broker_name']])) {
                $dataYm[$time][$row['broker_name']] = new ReportTeamData();
            }
            $dataYm[$time][$row['broker_name']]->add_row($row);
        }
        return array('all' => $data, 'ym' => $dataYm);
    }

    public static function buildReportBrokerData($filter)
    {
        return self::queryBroker($filter);
    }

    public static function buildReportBasicData($filter)
    {
        return self::queryBasic($filter);
    }

    public static function getApprovedTeam($filter, $combined = false)
    {
        return $combined ?
            self::convertSplitsToTeams(TeamStatsUtil::getApproved($filter, 'loansplits.approved'), 'approved')
            :
            self::convertSplitsToTeams(TeamPipelineUtil::getApproved($filter, 'loansplits.approved'), 'approved');
    }

    public static function getPendingTeam($filter, $combined = false)
    {
        return $combined ?
            self::convertSplitsToTeams(TeamStatsUtil::getPending($filter, 'loansplits.conditional'), 'conditional')
            :
            self::convertSplitsToTeams(TeamPipelineUtil::getPending($filter, 'loansplits.conditional'), 'conditional');
    }

    public static function getAipTeam($filter, $combined = false)
    {
        return $combined ?
            self::convertSplitsToTeams(TeamStatsUtil::getAip($filter, 'loansplits.aip'), 'aip')
            :
            self::convertSplitsToTeams(TeamPipelineUtil::getAip($filter, 'loansplits.aip'), 'aip');
    }

    public static function getSubmittedTeam($filter, $combined = false)
    {
        return $combined ?
            self::convertSplitsToTeams(TeamStatsUtil::getSubmitted($filter, 'loansplits.submitted'), 'submitted')
            :
            self::convertSplitsToTeams(TeamPipelineUtil::getSubmitted($filter, 'loansplits.submitted'), 'submitted');
    }

    public static function getCommittedTeam($filter, $combined = false)
    {
        return $combined ?
            self::convertSplitsToTeams(TeamStatsUtil::getCommitted($filter, 'loansplits.committed'), 'committed')
            :
            self::convertSplitsToTeams(TeamPipelineUtil::getCommitted($filter, 'loansplits.committed'), 'committed');
    }

    public static function getHotTeam($filter, $combined = false)
    {
        return $combined ?
            self::convertSplitsToTeams(TeamStatsUtil::getHot($filter, 'loansplits.hot'), 'hot')
            :
            self::convertSplitsToTeams(TeamPipelineUtil::getHot($filter, 'loansplits.hot'), 'hot');
    }

    public static function getSettledTeam($filter, $combined = false)
    {
        return $combined ?
            self::convertSplitsToTeams(TeamStatsUtil::getSettled($filter, 'loansplits.settled'), 'settled')
            :
            self::convertSplitsToTeams(TeamPipelineUtil::getSettled($filter, 'loansplits.settled'), 'settled');
    }


}


