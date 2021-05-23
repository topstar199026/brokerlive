<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Response;

use App\Http\Controllers\Util\FormatUtil;
use App\Http\Controllers\Util\UserUtil;
use App\Http\Controllers\Util\DealUtil;
use App\Http\Controllers\Util\JournalUtil;
use App\Http\Controllers\Util\LoanSplitUtil;
use App\Http\Controllers\Util\PreferenceUtil;

use App\Datas\DashboardData;

class PanelController extends Controller
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
        $splitsFinancedue = LoanSplitUtil::getFinancedue();
        $splitsSettlementdue = LoanSplitUtil::getSettlementdue();
        return view('pages.panel.index', ['splitsFinancedue' => $splitsFinancedue, 'splitsSettlementdue' => $splitsSettlementdue]);
    }
}
