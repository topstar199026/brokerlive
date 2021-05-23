<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use Response;

use App\Http\Controllers\Util\FormatUtil;
use App\Http\Controllers\Util\UserUtil;
use App\Http\Controllers\Util\DealUtil;
use App\Http\Controllers\Util\JournalUtil;
use App\Http\Controllers\Util\LoanSplitUtil;
use App\Http\Controllers\Util\PreferenceUtil;

use App\Datas\DashboardData;

class DashboardController extends Controller
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
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $data = array();
        $dateFrom = date("Y-m") . "-01 00:00:00";
        $dateTo = date("Y-m-d 00:00:00", strtotime("tomorrow"));

        $resStatistic = array();

        $leads = DealUtil::getLeadCount($dateFrom, $dateTo);
        $calls = JournalUtil::getCallCount($dateFrom, $dateTo);
        $appts = JournalUtil::getAppointmentCount($dateFrom, $dateTo);
        $submittedNumber = LoanSplitUtil::getSubmittedCount($dateFrom, $dateTo);
        $submitted = LoanSplitUtil::getSubmittedTotal($dateFrom, $dateTo);
        $pending = LoanSplitUtil::getPendingTotal($dateFrom, $dateTo);
        $unconditional = LoanSplitUtil::getUnconditionalTotal($dateFrom, $dateTo);
        $settled = LoanSplitUtil::getSubmittedTotal($dateFrom, $dateTo);

        $resStatistic = new DashboardData;
        $resStatistic->leads = $leads;
        $resStatistic->calls = $calls;
        $resStatistic->appts = $appts;
        $resStatistic->submittedNumber = $submittedNumber;
        $resStatistic->submitted = $submitted;
        $resStatistic->pending = $pending;
        $resStatistic->unconditional = $unconditional;
        $resStatistic->settled = $settled;


        $dateFrom = date("Y-m-d 00:00:00", strtotime("-6 months"));
        $dateTo = date("Y-m") . "-01 00:00:00";

        $leads = DealUtil::getLeadCount($dateFrom, $dateTo);
        $calls = JournalUtil::getCallCount($dateFrom, $dateTo);
        $appts = JournalUtil::getAppointmentCount($dateFrom, $dateTo);
        $submittedNumber = LoanSplitUtil::getSubmittedCount($dateFrom, $dateTo);
        $submitted = LoanSplitUtil::getSubmittedTotal($dateFrom, $dateTo);
        $pending = LoanSplitUtil::getPendingTotal($dateFrom, $dateTo);
        $unconditional = LoanSplitUtil::getUnconditionalTotal($dateFrom, $dateTo);
        $settled = LoanSplitUtil::getSettledTotal($dateFrom, $dateTo);

        $resMonthly = new DashboardData;
        $resMonthly->leads = $leads;
        $resMonthly->calls = $calls;
        $resMonthly->appts = $appts;
        $resMonthly->submittedNumber = $submittedNumber;
        $resMonthly->submitted = $submitted;
        $resMonthly->pending = $pending;
        $resMonthly->unconditional = $unconditional;
        $resMonthly->settled = $settled;

        /*  --------------------------------- */
        $userCommissionValue = PreferenceUtil::getPreference('commission_value');

        $leadValue = $leads>0 ? '$'.FormatUtil::numberFormat((($userCommissionValue/100) * $settled ) / $leads, 0) : '$0';
        $callValue = $calls>0 ? '$'.FormatUtil::numberFormat((($userCommissionValue/100) * $settled ) / $calls, 0) : '$0';
        $appointmentValue = $appts>0 ? '$'.FormatUtil::numberFormat((($userCommissionValue/100) * $settled ) / $appts, 0) : '$0';

        $today = date("Y-m") . "-01 00:00:00";
        $sixMonthsAgo = date("Y-m-d 00:00:00", strtotime("-6 months"));

        $avgPayTime = LoanSplitUtil::getAvgSettlementCommission($today, $sixMonthsAgo);

        $incomeValue = array();
        if ($avgPayTime == 0 || $userCommissionValue == 0)
        {
            for ($i = 0; $i < 3; $i++) {
                $incomeValue[date('F', strtotime("+$i months"))] = 0;
            }
        }
        else
        {
            for ($i = 0; $i < 3; $i++) {
                $dateFrom = date("Y-m", strtotime("+$i months")) . "-01";
                $dateTo = date("Y-m", strtotime("+". ($i + 1) ." months")) . "-01";

                $value = LoanSplitUtil::getIncomeTotal(
                    $dateFrom,
                    $dateTo,
                    $avgPayTime
                );

                $incomeValue[date('F', strtotime("+$i months"))] = $value * ($userCommissionValue/100);
            }

            // we get overdue and add them in the current month
            $overdueIncome = LoanSplitUtil::getConfirmedOverdueTotal(
                $sixMonthsAgo,
                $today,
                $avgPayTime
            );
            $incomeValue[date('F')] += $overdueIncome * ($userCommissionValue/100);

            $uncomfirmed_value = LoanSplitUtil::getUnconfirmedTotal(
                $sixMonthsAgo,
                $today
            );

            $incomeValue['Unconfirmed'] = $uncomfirmed_value * ($userCommissionValue/100);
        }

        /*  --------------------------------- */
        $dateFrom = date("Y-m", strtotime("-6 months")) . "-01 00:00:00";
        $dateTo = date("Y-m") . "-01 00:00:00";

        $avgappointmentsubmissionValue = FormatUtil::numberFormat(LoanSplitUtil::getAvgAppointmentSubmitted($dateFrom, $dateTo) ,0);
        $avgappointmentsettledValue = FormatUtil::numberFormat(LoanSplitUtil::getAvgAppointmentSettlement($dateFrom, $dateTo) ,0);
        $avgsettlementcommissionValue = FormatUtil::numberFormat(LoanSplitUtil::getAvgSettlementCommission($dateFrom, $dateTo) ,0);

        /*  --------------------------------- */
        $loans = [];
        $splits = LoanSplitUtil::getSettledLoans();
        $loans["settled"]["count"] = (int)$splits->loan_count;
        $loans["settled"]["value"] = (int)$splits->loan_value;

        $splits = LoanSplitUtil::getDischargedLoans();
        $loans["discharged"]["count"] = (int)$splits->loan_count;
        $loans["discharged"]["value"] = (int)$splits->loan_value;

        $splits = LoanSplitUtil::getActiveLoans();
        $loans["active"]["count"] = (int)$splits->loan_count;
        $loans["active"]["value"] = (int)$splits->loan_value;

        $summaryLoans = [];
        $date = date('Y-m-d 00:00:00', strtotime("-12 months"));

        $splits = LoanSplitUtil::getSettledLoans($date);
        $summaryLoans["settled"]["count"] = (int)$splits->loan_count;
        $summaryLoans["settled"]["value"] = (int)$splits->loan_value;

        $splits = LoanSplitUtil::getDischargedLoans($date);
        $summaryLoans["discharged"]["count"] = (int)$splits->loan_count;
        $summaryLoans["discharged"]["value"] = (int)$splits->loan_value;

        return view('pages.dashboard.index', [
            'statistic' => $resStatistic,
            'monthly' => $resMonthly,

            'leadValue' => $leadValue,
            'callValue' => $callValue,
            'appointmentValue' => $appointmentValue,

            'incomeValue' => $incomeValue,

            'avgappointmentsubmissionValue' => $avgappointmentsubmissionValue,
            'avgappointmentsettledValue' => $avgappointmentsettledValue,
            'avgsettlementcommissionValue' => $avgsettlementcommissionValue,

            'loans' => $loans,
            'summaryLoans' => $summaryLoans
        ]);
    }

    public function activeLoan(Request $request)
    {
        return LoanSplitUtil::getLoanChart('discharged', false);
    }

    public function settleLoan(Request $request)
    {
        return LoanSplitUtil::getLoanChart('settled', true);
    }

    public function yearSettleLoan(Request $request)
    {
        $dateFrom = date("Y-m-d h:i:s", strtotime("-12 months"));
        $dateTo = date("Y-m") . "-01 00:00:00";
        return LoanSplitUtil::getLoanChart('settled', true, $dateFrom, $dateTo);
    }

    public function checks()
    {
        //return Response()->json(count(DealUtil::getDealById('')));
        //return FormatUtil::numberFormat(233333,0);
        // return PreferenceUtil::getPreferenceJson();
        return Hash::make('1');
    }
}
