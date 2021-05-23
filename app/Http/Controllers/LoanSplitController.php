<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;

use App\Http\Controllers\Util\FormatUtil;
use App\Http\Controllers\Util\UserUtil;
use App\Http\Controllers\Util\DealUtil;
use App\Http\Controllers\Util\ContactUtil;
use App\Http\Controllers\Util\LoanSplitUtil;
use App\Http\Controllers\Util\LenderUtil;
use App\Http\Controllers\Util\ReminderUtil;
use App\Http\Controllers\Util\JournalUtil;

class LoanSplitController extends Controller
{
    public function loanSplit(Request $request)
    {
        // $request->method()
        $action = $request->route('action');
        $actionId = $request->route('id');
        switch ($action)
        {
            case 'edit':
                return $this::editLoanSplit($request, $actionId);
                break;
            case 'delete':
                return $this::deleteLoanSplit($request, $actionId);
                break;
        }
    }

    public function editLoanSplit(Request $request, $dealId)
    {
        $deal = DealUtil::getDealById($dealId);
        $resData = array();
        try
        {
            $tags = array(
                array(
                    "full app" => "Full App",
                    "pre app" => "Pre App",
                    "purchase" => "Purchase",
                    "refi" => "Refi",
                ),
                array(
                    "oo" => "O/O",
                    "inv" => "Inv",
                    "pi" => "P&I",
                    "io" => "I/O",
                ),
                array(
                    "fhb" => "FHB",
                    "fhog" => "FHOG",
                    "land" => "Land",
                    "construction" => "Const.",
                ),
                array(
                    "top up" => "Top Up",
                    "maintenance" => "Maint.",
                ),
            );
            $resData['tags'] = $tags;

            $splitData = DealUtil::getDocumentStatuses();
            $splitStatus = array();
            foreach ($splitData as $data) {
                if($data->name == "N\\A") {
                    $splitStatus[0] = $data;
                } else {
                    $splitStatus[$data->id] = $data;
                }
            }
            $resData['splitStatus'] = $splitStatus;

            $lenders = LenderUtil::getLenders();
            $resData['lenders'] = $lenders;
            $resData['otherId'] = -1;

            $resData['split'] = null;
            return view('pages.deal.loansplit.form', $resData);

        }
        catch(Exeption $e)
        {
            abort(404);
        }


    }

    public function saveLoanSplit(Request $request)
    {
        $id = $request->input('id');
        $data = Arr::except($request->all(), ['_token', 'id']);
        $split = LoanSplitUtil::saveLoanSplit($id, $data);

        return $split;
    }

    public function getLoanSplits(Request $request)
    {
        $dealId = $request->input('deal_id');

        $resData = array();

        $splits = LoanSplitUtil::getLoanSplitsByDealId($dealId);
        $resData['splits'] = $splits;

        $tags = array(
            array(
                "full app" => "Full App",
                "pre app" => "Pre App",
                "purchase" => "Purchase",
                "refi" => "Refi",
            ),
            array(
                "oo" => "O/O",
                "inv" => "Inv",
                "pi" => "P&I",
                "io" => "I/O",
            ),
            array(
                "fhb" => "FHB",
                "fhog" => "FHOG",
                "land" => "Land",
                "construction" => "Const.",
            ),
            array(
                "top up" => "Top Up",
                "maintenance" => "Maint.",
            ),
        );
        $resData['tags'] = $tags;

        $splitData = DealUtil::getDocumentStatuses();
        $splitStatus = array();
        foreach ($splitData as $data) {
            if($data->name == "N\\A") {
                $splitStatus[0] = $data;
            } else {
                $splitStatus[$data->id] = $data;
            }
        }
        $resData['splitStatus'] = $splitStatus;

        $lenders = LenderUtil::getLenders();
        $resData['lenders'] = $lenders;
        $resData['otherId'] = -1;

        //$resData['split'] = null;

        return view('pages.deal.loansplit.list', $resData);
    }

    public function deleteLoanSplit(Request $request, $loanSplitId)
    {
        $dealId = LoanSplitUtil::deleteLoanSplit($loanSplitId);
        return redirect('/deal/index/'.$dealId);
    }
}
