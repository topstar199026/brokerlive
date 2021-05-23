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
use App\Http\Controllers\Util\LenderUtil;


use App\Datas\DealAttribute;

class DealController extends Controller
{
    public function index(Request $request)
    {
        $dealId = $request->route('id');

        if(empty($dealId) || $dealId === '') return redirect()->intended('pipeline');

        $deal = DealUtil::getDealById($dealId);
        $resData = array();
        try
        {
            if (count($deal) > 0) {
                /*--- deal summary value start ---*/
                $noteTypes = DealUtil::getContentTags();
                $contactTypes = DealUtil::getContactTypes();
                $dealNotify = DealUtil::getDealNotify($dealId);
                if (count($dealNotify) > 0) {
                    $temp = $dealNotify;
                    $dealNotify = array();
                    foreach ($temp as $t) {
                        $dealNotify[$t->user_type] = 1;
                    }
                }
                //$roles = DealUtil::getRoles();

                $resData['deal'] = $deal[0];
                $resData['noteTypes'] = $noteTypes;
                $resData['contactTypes'] = $contactTypes;
                $resData['dealNotify'] = $dealNotify;
                
                /*--- deal summary value end ---*/
            } else {
                abort(404);
            }

            $attributes = new DealAttribute;
            $resData['attributes'] = $attributes;

            $dealTrees = DealUtil::getDealTree($deal[0]);
            $resData['dealTrees'] = $dealTrees;


            /*--- deal contact list value start ---*/
            $dealContacts = DealUtil::getDealContactsByDealId($dealId);
            $resData['contacts'] = $dealContacts;
            $resData['contactnum'] = 'aaa';
            /*--- deal contact list value end ---*/

            /*--- deal contact form value start ---*/
            $titles = DealUtil::getPersonTitles();
            $resData['titles'] = $titles;
            /*--- deal contact form value end ---*/

            /*--- lansplit list value start ---*/
            $splits = LoanSplitUtil::getLoanSplitsByDealId($dealId);
            $resData['splits'] = $splits;
            /*--- lansplit list value   end ---*/

            /*--- llansplitt from value start ---*/
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
            /*--- llansplitt from value end ---*/

            /*--- filemanagement list value start ---*/
            $files = DealUtil::getFilesByDealId($dealId);
            $resData['files'] = $files;
            /*--- filemanagement list value   end ---*/

            return view('pages.deal.index', $resData);

        }
        catch(Exeption $e)
        {
            abort(404);
        }
        

    }

    public function create(Request $request)
    {
        $dealName = $request->input('deal-name');
        $brokerId = $request->input('broker_id');

        $deal = DealUtil::createDeal($dealName, $brokerId);

        return $deal->id;
    }

    public function update(Request $request, $id)
    {
        $dealName = $request->input('name');

        $dealStatus = $request->input('status');

        $dealNotes = $request->input('notes');

        $dealName && DealUtil::updateDealByField($id, 'name', $dealName);
        $dealStatus && DealUtil::updateDealByField($id, 'status', $dealStatus);
        $dealNotes && DealUtil::updateDealByField($id, 'notes', $dealNotes);

        return true;
    }

    public function clone(Request $request, $id)
    {
        $dealName = $request->input('deal-name');

        return DealUtil::cloneDeal($id, $dealName);
    }

    /*-- API --*/
    public function show(Request $request)
    {
        $new = $request->input('new');
        $deal_status = $request->input('deal_status');
        $for = $request->input('for');
        $todate = $request->input('todate');
        $broker = $request->input('broker');

        $deals = DealUtil::getDeals($new, $deal_status, $for, $todate, $broker);
        
        $apiData = array();
        foreach ($deals as $deal) {
            if (is_null($deal->duedate)) {
                $deal->first_reminder = null;
            } else {
                $deal->first_reminder = array(
                    'duedate' => $deal->duedate,
                    'tags' => $deal->tags,
                    'details' => $deal->details
                );
            }
            $apiData[] = $deal;
        }
        return response()->json([
            'data' => $apiData,
            'error' => null,
            'status' => 'success'
        ]);
    }

    

   
    
     

    

    

}
