<?php

namespace App\Http\Controllers\Util;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;
use DB;

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
use App\Http\Controllers\Util\FormatUtil;
use App\Http\Controllers\Util\RelationUtil;

//----Aleksey
use Illuminate\Http\Request;
use App\Datas\ModelTreeDeal;
use App\Datas\ContactAutoList;
use App\Models\MaritalStatus;
use App\Models\ContactAddress;
use App\Models\ContactEmployment;
use App\Http\Controllers\Util\CommonDbUtil;
class Api_Model_Autocomplete_Suggestion {
    public $label;
    public $value;
    public $data;

    public function __construct($label, $value, $data)
    {
        $this->label = $label;
        $this->value = $value;
        $this->data = $data;
    }
}
//------end
class ContactUtil extends Controller
{
    public static function getContactById($id)
    {
        return Contact::find($id);
    }

    public static function saveContact(
            $id, $user_id, $contact_id, $persontitle_id,
            $firstname, $middlename, $lastname,
            $contacttype_id, $company, $phonemobile, $phonework, $phonehome, $phonefax, $email,
            $address1, $address2, $suburb, $state, $postcode, $notes, $deal_id
    )
    {
        $_enable = true; //!FormatUtil::checkStringEmpty($dealName);
        if($_enable)
        {

            if($id)
            {
                $updateDealContact = DealContact::find($id);

                $updateDealContact->persontitle_id = $persontitle_id;
                $updateDealContact->firstname = $firstname;
                $updateDealContact->middlename = $middlename;
                $updateDealContact->lastname = $lastname;
                $updateDealContact->contacttype_id = $contacttype_id;
                $updateDealContact->company = $company;
                $updateDealContact->phonemobile = $phonemobile;
                $updateDealContact->phonework = $phonework;
                $updateDealContact->phonehome = $phonehome;
                $updateDealContact->phonefax = $phonefax;
                $updateDealContact->email = $email;
                $updateDealContact->address1 = $address1;
                $updateDealContact->address2 = $address2;
                $updateDealContact->suburb = $suburb;
                $updateDealContact->state = $state;
                $updateDealContact->postcode = $postcode;
                $updateDealContact->notes = $notes;

                $updateDealContact->save();

                $updateContact = Contact::find($updateDealContact->contact_id);

                $updateContact->persontitle_id = $persontitle_id;
                $updateContact->firstname = $firstname;
                $updateContact->middlename = $middlename;
                $updateContact->lastname = $lastname;
                $updateContact->contacttype_id = $contacttype_id;
                $updateContact->company = $company;
                $updateContact->phonemobile = $phonemobile;
                $updateContact->phonework = $phonework;
                $updateContact->phonehome = $phonehome;
                $updateContact->phonefax = $phonefax;
                $updateContact->email = $email;
                $updateContact->address1 = $address1;
                $updateContact->address2 = $address2;
                $updateContact->suburb = $suburb;
                $updateContact->state = $state;
                $updateContact->postcode = $postcode;
                $updateContact->notes = $notes;

                $updateContact->save();

            }
            else
            {
                $deal = Deal::find($deal_id);

                if($contact_id)
                {

                }
                else
                {


                    $newContact = new Contact;

                    $newContact->deal_id = $deal_id;
                    $newContact->user_id = $deal->user_id;
                    $newContact->persontitle_id = $persontitle_id;
                    $newContact->firstname = $firstname;
                    $newContact->middlename = $middlename;
                    $newContact->lastname = $lastname;
                    $newContact->contacttype_id = $contacttype_id;
                    $newContact->company = $company;
                    $newContact->phonemobile = $phonemobile;
                    $newContact->phonework = $phonework;
                    $newContact->phonehome = $phonehome;
                    $newContact->phonefax = $phonefax;
                    $newContact->email = $email;
                    $newContact->address1 = $address1;
                    $newContact->address2 = $address2;
                    $newContact->suburb = $suburb;
                    $newContact->state = $state;
                    $newContact->postcode = $postcode;
                    $newContact->notes = $notes;

                    $newContact->save();

                    $newDealContact = new DealContact;

                    $newDealContact->contact_id = $newContact->id;
                    $newDealContact->deal_id = $deal_id;
                    $newDealContact->persontitle_id = $persontitle_id;
                    $newDealContact->firstname = $firstname;
                    $newDealContact->middlename = $middlename;
                    $newDealContact->lastname = $lastname;
                    $newDealContact->contacttype_id = $contacttype_id;
                    $newDealContact->company = $company;
                    $newDealContact->phonemobile = $phonemobile;
                    $newDealContact->phonework = $phonework;
                    $newDealContact->phonehome = $phonehome;
                    $newDealContact->phonefax = $phonefax;
                    $newDealContact->email = $email;
                    $newDealContact->address1 = $address1;
                    $newDealContact->address2 = $address2;
                    $newDealContact->suburb = $suburb;
                    $newDealContact->state = $state;
                    $newDealContact->postcode = $postcode;
                    $newDealContact->notes = $notes;

                    $newDealContact->save();
                }

            }
            return true;
        }
        else
            return false;
    }

    public static function deleteContact($contactId)
    {
        $deleteContact = DealContact::find($contactId);
        $deleteContact->delete();
        return $deleteContact->deal_id;
    }

    public static function searchContactList($userId, $term)
    {
        if(trim($term))
        {
            $contactList = Contact::where(function($query) use ($term) {
                $query->where('firstname', 'like', '%'.$term.'%')
                      ->orWhere('lastname', 'like', '%'.$term.'%')
                      ->orWhere('middlename', 'like', '%'.$term.'%')
                      ->orWhere('company', 'like', '%'.$term.'%');
            })
            //->whereIn('user_id',UserUtil::getBrockerIds())
            ->where(function($query) use ($userId){
                $query->whereIn('user_id',UserUtil::getBrockerIds())
                    ->orWhere('global_id', '=', self::getGlobalId());
            })
            ->get();
            $suggestions = array();
            if($contactList)
            {
                foreach($contactList as $contact)
                {
                    $suggestions[] = new ContactAutoList(
                        $contact->firstname . ' ' . $contact->lastname . ' <small class="text-muted">('. $contact->type->name .') - #'.$contact->id.'</small>',
                        "#{$contact->id} " . $contact->firstname . ' ' . $contact->lastname,
                        $contact
                    );
                }
            }
            return $suggestions;
        }
        else
        {
            return [];
        }
    }

    public static function searchDealContacttList($dealId, $term)
    {
        if(trim($term))
        {
            $dealContactList = DealContact::join('contacts', 'dealcontacts.contact_id', '=', 'contacts.id')
            ->where('contacts.user_id', '=', Auth::id())
            ->where('dealcontacts.deal_id', '=', $dealId)
            ->where(function($query) use ($term) {
                $query->where('dealcontacts.firstname', 'like', '%'.$term.'%')
                      ->orWhere('dealcontacts.lastname', 'like', '%'.$term.'%')
                      ->orWhere('dealcontacts.middlename', 'like', '%'.$term.'%')
                      ->orWhere('dealcontacts.company', 'like', '%'.$term.'%');
            })->get();
            $suggestions = array();
            if($dealContactList)
            {
                foreach($dealContactList as $contact)
                {
                    $suggestions[] = new ContactAutoList(
                        $contact->firstname . ' ' . $contact->lastname . ' <small class="text-muted">('. $contact->type->name .') - #'.$contact->id.'</small>',
                        "#{$contact->id} " . $contact->firstname . ' ' . $contact->lastname,
                        $contact
                    );
                }
            }
            return $suggestions;
        }
        else
        {
            return [];
        }
    }

    public static function toSimplePhone($phone)
    {
        return $phone = str_replace(' ', '', $phone);
        return ltrim($phone, '0');
    }

    /**------------------------------Aleksey---------------------------------- */
    public static function getContactType()
    {
        return ContactType::select()->get();
    }

    public static function getGlobalId()
    {
        if(Auth::user()->isOrganisationAdmin())
        {
            return RelationUtil::getOrgIdByUserId(Auth::id())->relation_id;
        }
        else
        return -1;
    }

    public static function datatable(Request $request, $type = null)
    {
        $contactType = $request->query("contact_type");
        $exposedColumns = array(
            "id",
            "lastname",
            "middlename",
            "firstname",
            "company",
            "phonemobile",
            "phonehome",
            "phonework",
            "email",
            "contacttype_id"
        );
        $query=Contact::select($exposedColumns);
        if(!empty($contactType) && $contactType !== 0) {
            $query = $query
                ->where('contacttype_id', "=", $contactType);
        }

        if($type !== null && $type == 'global'){
            $query = $query
                ->where('global_id', "=", self::getGlobalId());
        }else{
            $query = $query
                ->where('global_id', "=", 0);
        }

        $userId = Auth::user()->id;
        $queryUserId = trim($request->input('user_id'));
        if (!empty($queryUserId)) {
            // check whether current logged in user has access to the query user_id
            if (in_array($queryUserId, UserUtil::getBrockerIds())) {
                $userId = $queryUserId;
            }
        }
        $query = $query->where('user_id', "=", $userId);


        $columnMap = array('id', array('firstname', 'middlename', 'lastname'), 'company', array('phonemobile', 'phonehome', 'phonework'), 'phonefax', 'email');
        $searchParam = $request->query("search");
        if (is_array($searchParam)) {
            $search = $searchParam['value'];
        }

        if(!empty($search)) {
            $search = trim($search);
            if ($search[0] === "#" && strlen($search) > 1) {
                $firstSpace = strpos($search, " ");
                if (!$firstSpace) {
                    $firstSpace = strlen($search) - 1;
                }
                $query=$query
                    ->where("id", "=", substr($search, 1, $firstSpace));

                // clear the search query
                $request->query('search', '');
                $search="";
            }
        }

        $order = $request->query("order");
        if (!isset($order) || !is_array($order)) {
            $query = $query
                ->orderBy('lastname');
        }

        $draw = $request->query('draw');
        $start = $request->query("start");
        $length = $request->query("length");
        $order = $request->query('order');
        if (isset($totalData) && is_integer($totalData)) {
            $totalData = $totalData;
        } else {
            $totalData = $query->get()->count();
        }

        if (!empty($search)) {
            $query=CommonDbUtil::searchHelper($query,$search,$columnMap);
        }

        if (is_array($order)) {
            foreach ($order as $col) {
                $colNum = $col['column'];
                $dir = $col['dir'];
                $query=CommonDbUtil::orderHelper($query, $columnMap[$colNum], $dir);
            }
        }

        $totalFiltered = $query->get()->count();

        $data = $query
            ->limit($length)
            ->offset($start)
            ->get();
        $json_data = array(
            "draw"            => intval( $draw ),
            "recordsTotal"    => intval( $totalData ),
            "recordsFiltered" => intval( $totalFiltered ),
            "data"            => $data // total data array
        );

        return $json_data;
    }

    public static function _datatable(Request $request){
        $contactType = $request->query("contact_type");
        $exposedColumns = array(
            "id",
            "lastname",
            "middlename",
            "firstname",
            "company",
            "phonemobile",
            "phonehome",
            "phonework",
            "email",
            "contacttype_id"
        );
        $query=Contact::select($exposedColumns);
        if(!empty($contactType) && $contactType !== 0) {
            $query = $query
                ->where('contacttype_id', "=", $contactType);
        }

        $userId = Auth::user()->id;
        $queryUserId = trim($request->input('user_id'));
        if (!empty($queryUserId)) {
            // check whether current logged in user has access to the query user_id
            if (in_array($queryUserId, UserUtil::getBrockerIds())) {
                $userId = $queryUserId;
            }
        }
        $query = $query->where('user_id', "=", $userId);


        $columnMap = array('id', array('firstname', 'middlename', 'lastname'), 'company', array('phonemobile', 'phonehome', 'phonework'), 'phonefax', 'email');
        $searchParam = $request->query("search");
        if (is_array($searchParam)) {
            $search = $searchParam['value'];
        }

        if(!empty($search)) {
            $search = trim($search);
            if ($search[0] === "#" && strlen($search) > 1) {
                $firstSpace = strpos($search, " ");
                if (!$firstSpace) {
                    $firstSpace = strlen($search) - 1;
                }
                $query=$query
                    ->where("id", "=", substr($search, 1, $firstSpace));

                // clear the search query
                $request->query('search', '');
                $search="";
            }
        }

        $order = $request->query("order");
        if (!isset($order) || !is_array($order)) {
            $query = $query
                ->orderBy('lastname');
        }

        $draw = $request->query('draw');
        $start = $request->query("start");
        $length = $request->query("length");
        $order = $request->query('order');
        if (isset($totalData) && is_integer($totalData)) {
            $totalData = $totalData;
        } else {
            $totalData = $query->get()->count();
        }

        if (!empty($search)) {
            $query=CommonDbUtil::searchHelper($query,$search,$columnMap);
        }

        if (is_array($order)) {
            foreach ($order as $col) {
                $colNum = $col['column'];
                $dir = $col['dir'];
                $query=CommonDbUtil::orderHelper($query, $columnMap[$colNum], $dir);
            }
        }

        $totalFiltered = $query->get()->count();

        $data = $query
            ->limit($length)
            ->offset($start)
            ->get();
        $json_data = array(
            "draw"            => intval( $draw ),
            "recordsTotal"    => intval( $totalData ),
            "recordsFiltered" => intval( $totalFiltered ),
            "data"            => $data // total data array
        );

        return $json_data;
    }

    public static function autocomplete(Request $request){
        $term = trim($request->query('term'));
        $contactType = $request->query("contact_type");
        $exposedColumns = array(
            "id",
            "lastname",
            "middlename",
            "firstname",
            "company",
            "phonemobile",
            "phonehome",
            "phonework",
            "email",
            "contacttype_id"
        );
        $query=Contact::select($exposedColumns);
        if(!empty($contactType) && $contactType !== 0) {
            $query = $query->where('contacttype_id', "=", $contactType);
        }
        if ($term) {
            $query=CommonDbUtil::searchHelper($query,$term, array('firstname', 'lastname', 'middlename', 'company'));
            $list = $query->get();
            $suggestions = array();
            if(!empty($list)) {
                foreach($list as $contact)
                {
                    $suggestions[] = new Api_Model_Autocomplete_Suggestion(
                        $contact->firstname . ' ' . $contact->lastname . ' <small class="text-muted">('. $contact->contacttype_id .') - #'.$contact->id.'</small>',//type->name
                        "#{$contact->id} " . $contact->firstname . ' ' . $contact->lastname,
                        $contact->toArray()
                    );
                }
            }
            return $suggestions;
        }
    }
    public static function contact($id){
        return $id==0?new Contact:Contact::find($id);
    }
    public static function deal_contact($id){
        return DealContact::select()->where('contact_id','=',$id)->get();
    }
    public static function deal($id){
        return Deal::find($id);
    }
    public static function getPersonTitle(){
        return PersonTitle::select()->orderBy('id')->get();
    }
    public static function getMaritalStatus(){
        return MaritalStatus::select()->orderBy('id')->get();
    }
    public static function getContactAddress($id){
        return $id==0?null:ContactAddress::select()->where('contactid','=',$id)->get();
    }
    public static function getContactEmployment($id){
        $res = $id==0?ContactEmployment::select()->get():ContactEmployment::select()->where('contactid','=',$id)->get();
        foreach($res as $r)return $r;
        return null;
    }
    public static function getContactByUser($id){
        return $id==0?Contact::select()->where('user_id','=',Auth::id())->get():Contact::select()->where('id','!=',$id)->get();
    }
    public static function saveContactOne($id,$request,$type=null){
        $fields=array(
            'firstname',
            'middlename',
            'lastname',
            'company',
            'phonemobile',
            'phonework',
            'phonehome',
            'phonefax',
            'email',
            'address1',
            'address2',
            'suburb',
            'state',
            'postcode',
            'notes',
            'contacttype_id',
            'persontitle_id',
            'user_id',
            'deal_id',
            'work_address',
            'marital_status',
            'gender',
            'spouse',
            'kids',
            'oz_pr_os',
            'dob',
        );
        if($id>0){
            $row=Contact::find($id);
            foreach($fields as $field){
                if($request->input($field)!=null&&$request->input($field)!='null'){
                    if($field=='kids'){
                        if($request->input($field)>0)$row->$field=$request->input($field);
                    }else if($field=='dob'){
                        $row->$field=date('Y-m-d 00:00:00', strtotime($request->input($field)));
                    }
                    else $row->$field=$request->input($field);
                }
            }
            $row->updated_by=Auth::id();
            $row->updated_at=date("Y-m-d H:i:s");
            $row->save();

            ContactAddress::select()->where('contactid','=',$id)->delete();
            $address = $request->input("contact_address");
            if (!empty($address)) {
                foreach ($address as $addr) {
                    if(!isset($addr["homeaddress"])||$addr["homeaddress"]=='')continue;
                    $row = new ContactAddress;
                    $row->contactid = $id;
                    if(isset($addr["startdate"]))$addr["startdate"] = date("Y-m-d", strtotime($addr["startdate"]));
                    if(isset($addr["enddate"]))$addr["enddate"] = date("Y-m-d", strtotime($addr["enddate"]));
                    if (isset($addr['streetnumber']) && !$addr['streetnumber']) {
                        $addr['streetnumber'] = 0;
                    }
                    $fields=array(
                        'homeaddress',
                        'ownership',
                        'status',
                        'startdate',
                        'enddate',
                        'unit',
                        'streetnumber',
                        'streetname',
                        'streettype',
                        'suburb',
                        'state',
                        'postcode',
                        'country',
                    );
                    foreach($fields as $field){
                        if(isset($addr[$field]))$row->$field=$addr[$field];
                    }
                    $row->updated_by=Auth::id();
                    $row->updated_at=date("Y-m-d H:i:s");
                    $row->created_by=$row->updated_by;
                    $row->created_at=$row->updated_at;
                    $row->save();
                }
            }

            ContactEmployment::select()->where('contactid','=',$id)->delete();
            $employment = $request->input("contact_employment");
            if (!empty($employment)&&isset($employment["name"])&&$employment["name"]!='') {
                $row = new ContactEmployment;
                $row->contactid = $id;
                if(isset($employment["startdate"]))$employment["startdate"] = date("Y-m-d", strtotime($employment["startdate"]));
                if(isset($employment["enddate"]))$employment["enddate"] = date("Y-m-d", strtotime($employment["enddate"]));
                $fields=array(
                    'name',
                    'startdate',
                    'enddate',
                    'category',
                    'status'
                );
                foreach($fields as $field){
                    if(isset($employment[$field]))$row->$field=$employment[$field];
                }
                $row->updated_by=Auth::id();
                $row->updated_at=date("Y-m-d H:i:s");
                $row->created_by=$row->updated_by;
                $row->created_at=$row->updated_at;
                $row->save();
            }
        }else{
            $row=new Contact;
            foreach($fields as $field){
                if($request->input($field)!=null&&$request->input($field)!='null'){
                    if($field=='kids'){
                        if($request->input($field)>0)$row->$field=$request->input($field);
                    }else if($field=='dob'){
                        $row->$field=date('Y-m-d 00:00:00', strtotime($request->input($field)));
                    }
                    else $row->$field=$request->input($field);
                }
            }
            $row->deal_id=0;
            $row->user_id=Auth::id();
            $row->updated_by=Auth::id();
            $row->updated_at=date("Y-m-d H:i:s");
            $row->created_by=$row->updated_by;
            $row->created_at=$row->updated_at;
            if($type == 'global'){
                $row->global_id = self::getGlobalId();
            }
            if(isset($row->firstname)&&$row->firstname!=''&&$row->firstname!=null)
            $row->save();
        }
    }
    public static function delvalidate(Request $request){
        $data = array(
            "status" => 1,
            "message" => "success"
        );
        $contactId = $request->query("contact_id");
        $result = DealContact::select()->where('contact_id', "=", $contactId)->get();
        if(count($result) > 0) {
            $data = array(
                "status" => 0,
                "message" => "Unable to delete this contact, it is still linked to a deal."
            );
        }
        return $data;
    }
    public static function delete($id){
        Contact::find($id)->delete();
    }
}
