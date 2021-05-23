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

class ContactController extends Controller
{
    public function __construct()
    {

    }

    public function index(Request $request)
    {
        // $request->method()
        $action = $request->route('action');
        $actionId = $request->route('id');
        switch ($action)
        {
            case 'edit':
                return $this::edit($request, $actionId);
                break;
            case 'delete':
                return $this::delete($request, $actionId);
                break;
        }
    }

    public function edit(Request $request, $dealId)
    {
        $deal = DealUtil::getDealById($dealId);
        $resData = array();
        try
        {
            /*--- deal contact form value start ---*/
            $contactTypes = DealUtil::getContactTypes();
            $resData['contactTypes'] = $contactTypes;
            $titles = DealUtil::getPersonTitles();
            $resData['titles'] = $titles;
            $resData['contact'] = null;
            /*--- deal contact form value end ---*/
            return view('pages.deal.contact.form', $resData);

        }
        catch(Exeption $e)
        {
            abort(404);
        }


    }

    public function delete(Request $request, $contactId)
    {
        $dealId = ContactUtil::deleteContact($contactId);
        return redirect('/deal/index/'.$dealId);
    }

    public function create(Request $request)
    {
        $id = $request->input('id');
        $user_id = $request->input('user_id');
        $contact_id = $request->input('contact_id');
        $persontitle_id = $request->input('persontitle_id');
        $firstname = $request->input('firstname');
        $middlename = $request->input('middlename');
        $lastname = $request->input('lastname');
        $contacttype_id = $request->input('contacttype_id');
        $company = $request->input('company');
        $phonemobile = $request->input('phonemobile');
        $phonework = $request->input('phonework');
        $phonehome = $request->input('phonehome');
        $phonefax = $request->input('phonefax');
        $email = $request->input('email');
        $address1 = $request->input('address1');
        $address2 = $request->input('address2');
        $suburb = $request->input('suburb');
        $state = $request->input('state');
        $postcode = $request->input('postcode');
        $notes = $request->input('notes');
        $deal_id = $request->input('deal_id');

        return ContactUtil::saveContact(
            $id, $user_id, $contact_id, $persontitle_id,
            $firstname, $middlename, $lastname,
            $contacttype_id, $company, $phonemobile, $phonework, $phonehome, $phonefax, $email,
            $address1, $address2, $suburb, $state, $postcode, $notes, $deal_id
        );
    }

    public function getContacts(Request $request)
    {
        $dealId = $request->input('deal_id');
        $resData = array();
        $contactTypes = DealUtil::getContactTypes();
        $resData['contactTypes'] = $contactTypes;

        $dealContacts = DealUtil::getDealContactsByDealId($dealId);
        $resData['contacts'] = $dealContacts;
        $resData['contactnum'] = 'aaa';

        /*--- deal contact form value start ---*/
        $titles = DealUtil::getPersonTitles();
        $resData['titles'] = $titles;
        /*--- deal contact form value end ---*/


        return view('pages.deal.contact.list', $resData);
    }

    public function searchContactList(Request $request)
    {
        $userId = $request->input('user_id');
        $term = $request->input('term');
        return ContactUtil::searchContactList($userId, $term);
    }

    public function searchDealContacttList(Request $request)
    {
        $dealId = $request->input('deal_id');
        $term = $request->input('term');
        return ContactUtil::searchDealContacttList($dealId, $term);
    }
}
