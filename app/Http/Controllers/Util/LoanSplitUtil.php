<?php

namespace App\Http\Controllers\Util;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use DB;
use DateTime;


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

use App\Http\Controllers\Util\UserUtil;
use App\Http\Controllers\Util\WhiteboardUtil;
use App\Http\Controllers\Util\FormatUtil;

use App\Datas\WhiteBoardData;
use App\Datas\WhiteBoardDataRow;

class LoanSplitUtil extends Controller
{
    public static function getLoanSplitsByDealId($dealId)
    {
        return LoanSplit::where('deal_id', $dealId)
            ->get();
    }

    public static function saveLoanSplit($id, $data)
    {
        $split = $id ? LoanSplit::find($id) : new LoanSplit;
        $lenders = Lender::get();

        $split->subloan = FormatUtil::regularToNumber(data_get($data, 'subloan'));
        $split->submittedvalue = FormatUtil::regularToNumber(data_get($data, 'submittedvalue'));
        $split->aipvalue = FormatUtil::regularToNumber(data_get($data, 'aipvalue'));
        $split->conditionalvalue = FormatUtil::regularToNumber(data_get($data, 'conditionalvalue'));
        $split->approvedvalue = FormatUtil::regularToNumber(data_get($data, 'approvedvalue'));
        $split->settledvalue = FormatUtil::regularToNumber(data_get($data, 'settledvalue'));
        $split->submittedtrail = FormatUtil::regularToNumber(data_get($data, 'submittedtrail'));
        $split->aiptrail = FormatUtil::regularToNumber(data_get($data, 'aiptrail'));
        $split->conditionaltrail = FormatUtil::regularToNumber(data_get($data, 'conditionaltrail'));
        $split->approvedtrail = FormatUtil::regularToNumber(data_get($data, 'approvedtrail'));
        $split->settledtrail = FormatUtil::regularToNumber(data_get($data, 'settledtrail'));


        if(!Arr::exists($data, 'whiteboardhide'))
        {
            $split->whiteboardhide = 0;
            $split->notproceeding = null;
        }
        else
        {
            $split->whiteboardhide = 1;
            $split->notproceeding = FormatUtil::formatDateTime(data_get($data, 'notproceeding', null));
        }

        if(!Arr::exists($data, 'hotclient'))
        {
            $split->hotclient = 0;
        }
        else
        {
            $split->hotclient = 1;
        }

        if(!Arr::exists($data, 'committedclient'))
        {
            $split->committedclient = 0;
        }
        else
        {
            $split->committedclient = 1;
        }

        if(Arr::exists($data, 'commission_trail_applicable'))
        {
            $split->commission_trail_applicable = 1;
            $split->commission_paid_trail = FormatUtil::formatDateTime(data_get($data, 'commission_paid_trail', null));
        }
        else
        {
            $split->commission_trail_applicable = 0;
            $split->commission_paid_trail = null;
        }

        if(Arr::exists($data, 'commission_value_applicable'))
        {
            $split->commission_value_applicable = 1;
            $split->commission_paid_value = FormatUtil::formatDateTime(data_get($data, 'commission_paid_value', null));
        }
        else
        {
            $split->commission_value_applicable = 0;
            $split->commission_paid_value = null;
        }

        if(Arr::exists($data, 'tags'))
        {
            $split->tags =  implode(',',data_get($data, 'tags'));
        }
        if(Arr::exists($data, 'lender_id') && Arr::exists($data, 'other_lender_id') && data_get($data, 'lender_id') == data_get($data, 'other_lender_id'))
        {
            $split->lender = data_get($data, 'other_lender', null);
            foreach ($lenders as $lender) {
                $val1 = str_replace(' ', '', $lender->name);
                $val2 = str_replace(' ', '', $split->lender);
                if (strtolower($val1) == strtolower($val2)) {
                    $split->lender_id = $lender->id;
                    $split->lender = $lender->name;
                    break;
                }
            }
            if (!isset($split->lender_id)) {
                $split->lender_id = data_get($data, 'other_lender_id');
            }
        }
        else if(Arr::exists($data, 'lender_id') && Arr::exists($data, 'other_lender_id') && data_get($data, 'lender_id') <> data_get($data, 'other_lender_id'))
        {
            $split->lender_id = data_get($data, 'lender_id', null);
            foreach ($lenders as $lender) {
                if ($lender->id == data_get($data, 'lender_id')) {
                    $split->lender = $lender->name;
                    break;
                }
            }
        }

        $split->loan_number = data_get($data, 'loan_number', null);
        $split->split_number = data_get($data, 'split_number', null);
        $split->filenumber = data_get($data, 'filenumber', null);
        $split->lvr = data_get($data, 'lvr', null);
        $split->lmi = data_get($data, 'lmi', null);
        $split->financeduedate = FormatUtil::formatDateTime(data_get($data, 'financeduedate', null));
        $split->settlementdate = FormatUtil::formatDateTime(data_get($data, 'settlementdate', null));
        $split->referrer_id = data_get($data, 'referrer_id', null);
        //$split->referrer = data_get($data, 'referrer', null);  ??
        $split->product = data_get($data, 'product', null);
        $split->documentstatus_id = data_get($data, 'documentstatus_id', 0);  //??
        $split->initial_appointment = FormatUtil::formatDateTime(data_get($data, 'initial_appointment', null));
        $split->submitted = FormatUtil::formatDateTime(data_get($data, 'submitted', null));
        $split->conditional = FormatUtil::formatDateTime(data_get($data, 'conditional', null));
        $split->approved = FormatUtil::formatDateTime(data_get($data, 'approved', null));
        $split->settled = FormatUtil::formatDateTime(data_get($data, 'settled', null));
        $split->discharged = FormatUtil::formatDateTime(data_get($data, 'discharged', null));
        $split->aip = FormatUtil::formatDateTime(data_get($data, 'aip', null));




        $split->deal_id = data_get($data, 'deal_id');

        $split->save();

        $applicantIds = data_get($data, 'applicant_ids', null);
        self::saveApplicant($split, $applicantIds);
        return $split;
    }

    public static function saveApplicant($split, $applicantIds)
    {
        // Log::instance()->add(
        //     Log::DEBUG,
        //     'Saving loan split applicants, :applicantIds',
        //     array(
        //         ':applicantIds' => print_r($applicantIds, true)
        //     )
        // )->write();
        self::removeApplicants($split);
        if (!$applicantIds) {
            return;
        }

        foreach ($applicantIds as $applicantId) {
            $newLoanApplicant = new LoanApplicant;
            $newLoanApplicant->loansplit_id = $split->id;
            $newLoanApplicant->applicant_id = $applicantId;
            $newLoanApplicant->save();
        }
    }

    public static function removeApplicants($split)
    {
        LoanApplicant::where('loansplit_id', '=', $split->id)->delete();
    }

    public static function deleteLoanSplit($loanSplitId)
    {
        $deleteLoanSplit = LoanSplit::find($loanSplitId);
        self::removeApplicants($deleteLoanSplit);
        $deleteLoanSplit->delete();
        return $deleteLoanSplit->deal_id;
    }

    public static function getSubmittedCount($dateFrom, $dateTo)
    {
        return LoanSplit::join('deals', 'deals.id', '=', 'loansplits.deal_id')
            ->whereIn('deals.user_id', UserUtil::getBrockerIds())
            ->whereNotNull('loansplits.submitted')
            ->where('loansplits.submitted', '>=', $dateFrom)
            ->where('loansplits.submitted', '<', $dateTo)
            ->get()
            ->count();
    }

    public static function getSubmittedTotal($dateFrom, $dateTo)
    {
        return LoanSplit::join('deals', 'deals.id', '=', 'loansplits.deal_id')
            ->whereIn('deals.user_id', UserUtil::getBrockerIds())
            ->whereNotNull('loansplits.submitted')
            ->where('loansplits.submitted', '>=', $dateFrom)
            ->where('loansplits.submitted', '<', $dateTo)
            ->sum('loansplits.subloan');
    }

    public static function getPendingTotal($dateFrom, $dateTo)
    {
        return LoanSplit::join('deals', 'deals.id', '=', 'loansplits.deal_id')
            ->whereIn('deals.user_id', UserUtil::getBrockerIds())
            ->whereNotNull('loansplits.conditional')
            ->where('loansplits.conditional', '>=', $dateFrom)
            ->where('loansplits.conditional', '<', $dateTo)
            ->sum('loansplits.conditionalvalue');
    }

    public static function getUnconditionalTotal($dateFrom, $dateTo)
    {
        return LoanSplit::join('deals', 'deals.id', '=', 'loansplits.deal_id')
            ->whereIn('deals.user_id', UserUtil::getBrockerIds())
            ->whereNotNull('loansplits.approved')
            ->where('loansplits.approved', '>=', $dateFrom)
            ->where('loansplits.approved', '<', $dateTo)
            ->sum('loansplits.approvedvalue');
    }

    public static function getSettledTotal($dateFrom, $dateTo)
    {
        return LoanSplit::join('deals', 'deals.id', '=', 'loansplits.deal_id')
            ->whereIn('deals.user_id', UserUtil::getBrockerIds())
            ->whereNotNull('loansplits.settled')
            ->where('loansplits.settled', '>=', $dateFrom)
            ->where('loansplits.settled', '<', $dateTo)
            ->sum('loansplits.settledvalue');
    }

    public static function getAvgSettlementCommission($dateFrom, $dateTo)
    {
        $value = LoanSplit::join('deals', 'deals.id', '=', 'loansplits.deal_id')
            ->whereIn('deals.user_id', UserUtil::getBrockerIds())
            ->whereNotNull('loansplits.commission_paid_value')
            ->whereNotNull('loansplits.settled')
            ->where('loansplits.commission_paid_value', '>=', $dateFrom)
            ->where('loansplits.commission_paid_value', '<', $dateTo)
            ->avg(DB::raw('DATEDIFF(loansplits.commission_paid_value, loansplits.settled)'));
        return $value == 0 ? 40 : $value;
    }

    public static function getIncomeTotal($dateFrom, $dateTo, $avgPayTime)
    {
        return LoanSplit::join('deals', 'deals.id', '=', 'loansplits.deal_id')
            ->whereIn('deals.user_id', UserUtil::getBrockerIds())
            ->where('loansplits.commission_value_applicable', '=', 1)
            ->where('loansplits.whiteboardhide', '=', 0)
            ->where(function($query) {
                $query->whereNotNull('loansplits.settled')
                      ->orWhereNotNull('loansplits.settlementdate');
            })
            ->where(DB::raw('DATE_ADD(COALESCE(loansplits.settled, loansplits.settlementdate), INTERVAL '.$avgPayTime.' DAY)'), '>=', $dateFrom)
            ->where(DB::raw('DATE_ADD(COALESCE(loansplits.settled, loansplits.settlementdate), INTERVAL '.$avgPayTime.' DAY)'), '<', $dateTo)
            ->sum(DB::raw('CASE
                    WHEN settled IS NOT NULL THEN settledvalue
                    WHEN approved IS NOT NULL THEN approvedvalue
                    WHEN conditional IS NOT NULL THEN conditionalvalue
                    ELSE 0
            END'));
    }

    public static function getConfirmedOverdueTotal($dateFrom, $dateTo, $avgPayTime)
    {
        return LoanSplit::join('deals', 'deals.id', '=', 'loansplits.deal_id')
            ->whereIn('deals.user_id', UserUtil::getBrockerIds())
            ->where('loansplits.commission_value_applicable', '=', 1)
            ->where('loansplits.whiteboardhide', '=', 0)
            ->whereNotNull('loansplits.settled')
            ->whereNull('loansplits.commission_paid_value')
            ->where(DB::raw('DATE_ADD(loansplits.settled, INTERVAL '.$avgPayTime.' DAY)'), '>=', $dateFrom)
            ->where(DB::raw('DATE_ADD(loansplits.settled, INTERVAL '.$avgPayTime.' DAY)'), '<', $dateTo)
            ->sum('loansplits.settledvalue');
    }

    public static function getUnconfirmedTotal($dateFrom, $dateTo)
    {
        return LoanSplit::join('deals', 'deals.id', '=', 'loansplits.deal_id')
            ->whereIn('deals.user_id', UserUtil::getBrockerIds())
            ->where('loansplits.commission_value_applicable', '=', 1)
            ->where('loansplits.whiteboardhide', '=', 0)
            ->whereNull('loansplits.settled')
            ->whereNull('loansplits.settlementdate')
            ->where(function($query) {
                $query->whereNotNull('loansplits.approved')
                      ->orWhereNotNull('loansplits.conditional');
            })
            ->where(DB::raw('COALESCE(loansplits.approved, loansplits.conditional)'), '>=', $dateFrom)
            ->where(DB::raw('COALESCE(loansplits.approved, loansplits.conditional)'), '<', $dateTo)
            ->sum(DB::raw('CASE
                WHEN approved IS NOT NULL THEN approvedvalue
                WHEN conditional IS NOT NULL THEN conditionalvalue
                ELSE 0
            END'));
    }

    public static function getAvgAppointmentSubmitted($dateFrom, $dateTo)
    {
        return LoanSplit::join('deals', 'deals.id', '=', 'loansplits.deal_id')
            ->whereIn('deals.user_id', UserUtil::getBrockerIds())
            ->whereNotNull('loansplits.initial_appointment')
            ->whereNotNull('loansplits.submitted')
            ->whereNotNull('loansplits.settled')
            ->where('loansplits.settled', '>=', $dateFrom)
            ->where('loansplits.settled', '<', $dateTo)
            ->avg(DB::raw('DATEDIFF(loansplits.submitted, loansplits.initial_appointment)'));
    }

    public static function getAvgAppointmentSettlement($dateFrom, $dateTo)
    {
        return LoanSplit::join('deals', 'deals.id', '=', 'loansplits.deal_id')
            ->whereIn('deals.user_id', UserUtil::getBrockerIds())
            ->whereNotNull('loansplits.initial_appointment')
            ->whereNotNull('loansplits.settled')
            ->where('loansplits.settled', '>=', $dateFrom)
            ->where('loansplits.settled', '<', $dateTo)
            ->avg(DB::raw('DATEDIFF(loansplits.settled, loansplits.initial_appointment)'));
    }

    public static function getSettledLoans($date = null)
    {
        $result = LoanSplit::leftJoin('deals', 'deals.id', '=', 'loansplits.deal_id')
        ->whereIn('deals.user_id', UserUtil::getBrockerIds())
        ->whereNotNull('loansplits.settled');

        $date &&
            $result->where('loansplits.settled', '>=', $date);

        return $result->select(DB::raw('COUNT(1) AS loan_count'), DB::raw('SUM(settledtrail) AS loan_value'))
            ->get()
            ->first();
    }

    public static function getDischargedLoans($date = null)
    {
        $result = LoanSplit::leftJoin('deals', 'deals.id', '=', 'loansplits.deal_id')
        ->whereIn('deals.user_id', UserUtil::getBrockerIds())
        ->whereNotNull('loansplits.discharged');

        $date &&
            $result->where('loansplits.discharged', '>=', $date);

        return $result->select(DB::raw('COUNT(1) AS loan_count'), DB::raw('SUM(settledtrail) AS loan_value'))
            ->get()
            ->first();
    }

    public static function getActiveLoans()
    {
        return LoanSplit::leftJoin('deals', 'deals.id', '=', 'loansplits.deal_id')
        ->whereIn('deals.user_id', UserUtil::getBrockerIds())
        ->whereNotNull('loansplits.settled')
        ->whereNull('loansplits.discharged')
        ->select(DB::raw('COUNT(1) AS loan_count'), DB::raw('SUM(settledtrail) AS loan_value'))
        ->get()
        ->first();
    }

    public static function getLoanChart($con, $flag, $dateFrom = null, $dateTo = null)
    {
        $loanSplit = LoanSplit::Join('deals', 'deals.id', '=', 'loansplits.deal_id')
        ->whereIn('deals.user_id', UserUtil::getBrockerIds());

        if($flag) $loanSplit = $loanSplit->whereNotNull('loansplits.'.$con);
        else $loanSplit = $loanSplit->whereNull('loansplits.'.$con);

        $dateFrom &&
            $loanSplit = $loanSplit->where('loansplits.settled', '>=', $dateFrom);

        $dateTo &&
            $loanSplit = $loanSplit->where('loansplits.settled', '<', $dateTo);

        $loanSplit = $loanSplit->select('lender', DB::raw('COUNT(*) AS count'))
        ->groupBy('loansplits.lender_id')
        ->orderBy('count', 'DESC')
        ->get();

        return array(
            'data' => $loanSplit,
            'error' => null,
            'status' => 'success'
        );
    }

    public static function getFinancedue()
    {
        return LoanSplit::leftJoin('deals', 'deals.id', '=', 'loansplits.deal_id')
            ->whereIn('deals.user_id', UserUtil::getBrockerIds())
            ->whereNull('loansplits.approved')
            ->whereNotNull('loansplits.financeduedate')
            ->where('loansplits.financeduedate', '>=', date('Y-m-d 00:00:00', strtotime("today")))
            ->where('loansplits.financeduedate', '<', date('Y-m-d 00:00:00', strtotime("+7 days")))
            ->orderBy('loansplits.financeduedate', 'ASC')
            ->get();
    }

    public static function getSettlementdue()
    {
        return LoanSplit::leftJoin('deals', 'deals.id', '=', 'loansplits.deal_id')
            ->whereIn('deals.user_id', UserUtil::getBrockerIds())
            ->whereNull('loansplits.settled')
            ->whereNotNull('loansplits.settlementdate')
            ->where('loansplits.settlementdate', '>=', date('Y-m-d 00:00:00', strtotime("today")))
            ->where('loansplits.settlementdate', '<', date('Y-m-d 00:00:00', strtotime("+7 days")))
            ->orderBy('loansplits.settlementdate', 'ASC')
            ->get();
    }

    private static function baseCombinedQuery()
    {
        return LoanSplit::leftJoin('deals', 'deals.id', '=', 'loansplits.deal_id')
            ->whereIn('deals.user_id', WhiteboardUtil::getBrokerList());
    }

    private static function basePipelineQuery()
    {
        return LoanSplit::leftJoin('deals', 'deals.id', '=', 'loansplits.deal_id')
            ->whereIn('deals.user_id', WhiteboardUtil::getBrokerList())
            ->whereNull('loansplits.notproceeding');
    }

    private static function filterQuery($data, $filter, $column)
    {
        if ($filter['fromDate'])
        {
            $data = $data->where($column, '>=', date('Y-m-d 00:00:00', strtotime($filter['fromDate'])));
        }
        if ($filter['toDate'])
        {
            $data = $data->where($column, '<=', date('Y-m-d 23:59:59', strtotime($filter['toDate'])));
        }
        return $data;
    }

    private static function orderQuery($data, $column)
    {
        return $data->orderBy(DB::raw('CASE WHEN ' . $column . ' IS NULL THEN 1 ELSE 0 END'))
            ->orderBy($column, 'desc');
    }

    private static function getApprovedCombined($filter, $column)
    {
        $data = self::baseCombinedQuery();

        $data = $data->whereNotNull($column);
        $data = self::filterQuery($data, $filter, $column);
        $data = self::orderQuery($data, $column);

        return $data->get();
    }

    private static function getApproved($filter, $column)
    {
        $data = self::basePipelineQuery();

        $data = $data->where(function($query) use ($column) {
            $query->whereNotNull($column)
                ->whereNull('loansplits.settled');
        });

        $data = self::filterQuery($data, $filter, $column);
        $data = self::orderQuery($data, 'loansplits.settlementdate');

        return $data->get();
    }

    private static function getPendingCombined($filter, $column)
    {
        $data = self::baseCombinedQuery();

        $data = $data->whereNotNull($column);
        $data = self::filterQuery($data, $filter, $column);
        $data = self::orderQuery($data, $column);

        return $data->get();
    }

    private static function getPending($filter, $column)
    {
        $data = self::basePipelineQuery();

        $data = $data->where(function($query) use ($column) {
            $query->whereNotNull($column)
                ->whereNull('loansplits.approved')
                ->whereNull('loansplits.settled');
        });

        $data = self::filterQuery($data, $filter, $column);
        $data = self::orderQuery($data, 'loansplits.financeduedate');

        return $data->get();
    }

    private static function getAipCombined($filter, $column)
    {
        $data = self::baseCombinedQuery();

        $data = $data->whereNotNull($column);
        $data = self::filterQuery($data, $filter, $column);
        $data = self::orderQuery($data, $column);

        return $data->get();
    }

    private static function getAip($filter, $column)
    {
        $data = self::basePipelineQuery();

        $data = $data->where(function($query) use ($column) {
            $query->whereNotNull($column)
                ->whereNull('loansplits.conditional')
                ->whereNull('loansplits.approved')
                ->whereNull('loansplits.settled');
        });

        $data = self::filterQuery($data, $filter, $column);
        $data = self::orderQuery($data, 'loansplits.aip');

        return $data->get();
    }

    private static function getSubmittedCombined($filter, $column)
    {
        $data = self::baseCombinedQuery();

        $data = $data->whereNotNull($column);
        $data = self::filterQuery($data, $filter, $column);
        $data = self::orderQuery($data, $column);

        return $data->get();
    }

    private static function getSubmitted($filter, $column)
    {
        $data = self::basePipelineQuery();

        $data = $data->where(function($query) use ($column) {
            $query->whereNotNull($column)
                ->whereNull('loansplits.aip')
                ->whereNull('loansplits.conditional')
                ->whereNull('loansplits.approved')
                ->whereNull('loansplits.settled');
        });

        $data = self::filterQuery($data, $filter, $column);
        $data = self::orderQuery($data, 'loansplits.aip');

        return $data->get();
    }

    private static function getCommitted($filter, $column)
    {
        $data = self::basePipelineQuery();

        $data = $data->where('loansplits.committedclient', '=', 1);

        $data = self::orderQuery($data, 'loansplits.submitted');

        return $data->get();
    }

    private static function getHot($filter, $column)
    {
        $data = self::basePipelineQuery();

        $data = $data->where('loansplits.hotclient', '=', 1);

        $data = self::orderQuery($data, 'loansplits.submitted');

        return $data->get();
    }

    private static function getSettledCombined($filter, $column)
    {
        $data = self::baseCombinedQuery();

        $data = $data->whereNotNull($column);
        $data = self::filterQuery($data, $filter, $column);
        $data = self::orderQuery($data, $column);

        return $data->get();
    }

    private static function getSettled($filter, $column)
    {
        $data = self::basePipelineQuery();

        $data = $data->whereNotNull($column);

        $data = self::filterQuery($data, $filter, $column);
        $data = self::orderQuery($data, 'loansplits.settlementdate');

        return $data->get();
    }

    private static function getBusiness($filter, $column)
    {
        $data = self::baseCombinedQuery();

        $data = $data->where(function($query) use ($column) {
            $query
                ->whereNotNull('loansplits.initial_appointment')
                ->orWhereNotNull('loansplits.submitted')
                ->orWhereNotNull('loansplits.approved')
                ->orWhereNotNull('loansplits.settled')
                ->orWhereNotNull('loansplits.discharged')
                ->orWhereNotNull('loansplits.commission_paid_value')
                ->orWhereNotNull('loansplits.commission_paid_trail');
        });

        return $data->get();
    }

    public static function convertSplitsToWhiteboards($splits, $sectionName)
    {
        $whiteBoardData = new WhiteBoardData($sectionName);
        foreach($splits as $split)
        {
            $referrer = $split->referrer;
            if ($referrer) {

            }else{
                $referrer = $split->deal->referrer();
            }

            $whiteBoardDataRow = new WhiteBoardDataRow();

            $whiteBoardDataRow->deal_id = $split->deal->id;
            $whiteBoardDataRow->deal_status = $sectionName;
            $whiteBoardDataRow->broker_code = '';
            $whiteBoardDataRow->broker = substr($split->deal->broker->firstname, 0, 1) . substr($split->deal->broker->lastname, 0, 1);
            $whiteBoardDataRow->borrower = $split->deal->name;
            $whiteBoardDataRow->settlement_date = FormatUtil::formatDateTime2($split->settlementdate);

            $whiteBoardDataRow->referrer = '';
            if ($referrer != null)
            {
                try {

                    $whiteBoardDataRow->referrer = $referrer->lastname . ', ' . $referrer->firstname;

                } catch (\Exception $e) {

                    $whiteBoardDataRow->referrer = '';
                }
            }

            $whiteBoardDataRow->lender_id = '';
            $whiteBoardDataRow->lender = $split->lender;
            $whiteBoardDataRow->filenumber = $split->filenumber;
            $whiteBoardDataRow->loan_amount = $split->subloan;

            switch($sectionName) {
                case 'aip':
                    $whiteBoardDataRow->actual = $split->aipvalue;
                    $whiteBoardDataRow->month = date("M",strtotime($split->aip));
                    break;
                case 'conditional':
                    $whiteBoardDataRow->actual = $split->conditionalvalue;
                    $whiteBoardDataRow->month = date("M",strtotime($split->conditional));
                    break;
                case 'approved':
                    $whiteBoardDataRow->actual = $split->approvedvalue;
                    $whiteBoardDataRow->month = date("M",strtotime($split->approved));
                    break;
                case 'settled':
                    $whiteBoardDataRow->actual = $split->settledvalue;
                    $whiteBoardDataRow->month = date("M",strtotime($split->settled));
                    break;
                case 'submitted':
                    $whiteBoardDataRow->actual = $split->submittedvalue;
                    $whiteBoardDataRow->month = date("M",strtotime($split->submitted));
                    break;
            }

            $whiteBoardDataRow->doc_status = $split->documentstatus->name;
            $whiteBoardDataRow->finance_due = FormatUtil::formatDateTime2($split->financeduedate);
            $whiteBoardDataRow->submitted_date = FormatUtil::formatDateTime2($split->submitted);
            $whiteBoardDataRow->aip = FormatUtil::formatDateTime2($split->aip);
            $whiteBoardDataRow->conditional = FormatUtil::formatDateTime2($split->conditional);
            $whiteBoardDataRow->full_approval = FormatUtil::formatDateTime2($split->approved);

            $whiteBoardData->addRow($whiteBoardDataRow);
        }

        return $whiteBoardData;
    }

    private static function diffDate($val1, $val2)
    {
        if (empty($val1) || empty($val2))
        {
            return 0;
        } else {
                $datetime1 = new DateTime('@'.strtotime($val1));
                $datetime2 = new DateTime('@'.strtotime($val2));
                $interval = $datetime1->diff($datetime2);
                return $interval->format('%a days');
        }
    }

    private static function convertSplitsToBusiness($splits, $sectionName)
    {
        $result = array();
        foreach($splits as $split) {
            $data = array();
            $data["borrower"] = $split->deal->name;
            $data["id"] = $split->deal->id;
            $data["appts"] = empty($split->initial_appointment) ? '' : date("d/m/Y", strtotime($split->initial_appointment));
            $data["submitted"] = empty($split->submitted) ? '' : date("d/m/Y", strtotime($split->submitted));
            $data["approved"] = empty($split->approved) ? '' : date("d/m/Y", strtotime($split->approved));
            $data["settled"] = empty($split->settled) ? '' : date("d/m/Y", strtotime($split->settled));
            $data["discharged"] = empty($split->discharged) ? '' : date("d/m/Y", strtotime($split->discharged));
            $data["upfront"] = $split->commission_value_applicable == 0 ? 'N/A' : (empty($split->commission_paid_value) ? '' : date("d/m/Y", strtotime($split->commission_paid_value)));
            $data["trail"] = $split->commission_trail_applicable == 0 ? 'N/A' : (empty($split->commission_paid_trail) ? '' : date("d/m/Y", strtotime($split->commission_paid_trail)));
            $data["appts_submitted"] = self::diffDate($split->initial_appointment, $split->submitted);
            $data["submitted_approved"] = self::diffDate($split->submitted, $split->approved);
            $data["approved_settled"] = self::diffDate($split->approved, $split->settled);
            $data["settled_upfront"] = self::diffDate($split->settled, $split->commission_paid_value);
            $data["settled_discharge"] = self::diffDate($split->settled, $split->commission_paid_trail);
            $data["loan_life"] = self::diffDate($split->settled, $split->discharged);
            $data["last_activity"] = $split->deal->last_journal_activity;
            $result[] = $data;
        }
        return $result;
    }

    public static function getApprovedWhiteboard($filter, $combined = false)
    {
        return $combined ?
            self::convertSplitsToWhiteboards(self::getApprovedCombined($filter, 'loansplits.approved'), 'approved')
            :
            self::convertSplitsToWhiteboards(self::getApproved($filter, 'loansplits.approved'), 'approved');
    }

    public static function getPendingWhiteboard($filter, $combined = false)
    {
        return $combined ?
            self::convertSplitsToWhiteboards(self::getPendingCombined($filter, 'loansplits.conditional'), 'conditional')
            :
            self::convertSplitsToWhiteboards(self::getPending($filter, 'loansplits.conditional'), 'conditional');
    }

    public static function getAipWhiteboard($filter, $combined = false)
    {
        return $combined ?
            self::convertSplitsToWhiteboards(self::getAipCombined($filter, 'loansplits.aip'), 'aip')
            :
            self::convertSplitsToWhiteboards(self::getAip($filter, 'loansplits.aip'), 'aip');
    }

    public static function getSubmittedWhiteboard($filter, $combined = false)
    {
        return $combined ?
            self::convertSplitsToWhiteboards(self::getSubmittedCombined($filter, 'loansplits.submitted'), 'submitted')
            :
            self::convertSplitsToWhiteboards(self::getSubmitted($filter, 'loansplits.submitted'), 'submitted');
    }

    public static function getCommittedWhiteboard($filter, $combined = false)
    {
        return $combined ?
            self::convertSplitsToWhiteboards(array(), 'committed')
            :
            self::convertSplitsToWhiteboards(self::getCommitted($filter, 'loansplits.committed'), 'committed');
    }

    public static function getHotWhiteboard($filter, $combined = false)
    {
        return $combined ?
            self::convertSplitsToWhiteboards(array(), 'hot')
            :
            self::convertSplitsToWhiteboards(self::getHot($filter, 'loansplits.hot'), 'hot');
    }

    public static function getSettledWhiteboard($filter, $combined = false)
    {
        return $combined ?
            self::convertSplitsToWhiteboards(self::getSettledCombined($filter, 'loansplits.settled'), 'settled')
            :
            self::convertSplitsToWhiteboards(self::getSettled($filter, 'loansplits.settled'), 'settled');
    }

    public static function getBusinessWhiteboard($filter, $combined = false)
    {
        return self::convertSplitsToBusiness(self::getBusiness($filter, 'loansplits.settled'), 'settled');
    }

}
