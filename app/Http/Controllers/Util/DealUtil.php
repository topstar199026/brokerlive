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

use App\Datas\ModelTreeDeal;
use App\Datas\ContactAutoList;



class DealUtil extends Controller
{
    public static function log()
    {
        return Auth::user();
    }

    public static function getStatus()
    {
        if(UserUtil::login())
        {
            return Dealstatus::get();
        }
        else
        {
            return null;
        }
    }

    public static function getDealStatus()
    {
        return Dealstatus::get();
    }

    public static function getDealStatusById($id)
    {
        return Dealstatus::find($id);
    }

    public static function getStatusForSelect()
    {
        return array_reduce(Dealstatus::get()->toArray(), function ($result, $item) {
            $result[$item['id']] = $item['description'];
            return $result;
        }, array());
    }

    public static function getDeals($new, $deal_status, $for, $todate, $broker)
    {
        $deal =
            Deal::whereIn('user_id',UserUtil::getBrockerIds())
            ->leftJoin('deal_notify', 'deals.id', '=', 'deal_notify.deal_id')
            ->leftJoin('reminders', 'deals.id', '=', 'reminders.deal_id')
            ->where(function($query) {
                $query->whereNull('reminders.completed')
                      ->orWhereNotNull('deal_notify.id');
            });

        $deal_status &&
            $deal->where('deals.status', '=', $deal_status);

        $for &&
            $deal->where(function($query) use ($for) {
                $tags = explode(',', $for);
                foreach ($tags as $tag) {
                    $query->orWhere(DB::raw('FIND_IN_SET(\''.$tag.'\', `reminders`.`who_for`)'), '>', '0');
                }
            });

        $todate &&
            $deal->where(function ($query) use ($todate) {
                $query->where('reminders.duedate', '<=', date('Y-m-d 00:00:00', strtotime($todate)))
                    ->orWhereNull('reminders.duedate');
            });


        $broker &&
            $deal->whereIn('deals.user_id', explode(',',$broker));

        $deal->select(
            'deals.*',
            DB::raw('UNIX_TIMESTAMP(reminders.duedate) AS duedate'),
            DB::raw('CASE WHEN deal_notify.id IS NULL THEN 0 ELSE deal_notify.id END AS notify'),
            DB::raw('CASE WHEN reminders.tags IS NULL THEN 0 ELSE FIND_IN_SET(\'Urgent\', reminders.tags) END AS urgent'),
            'reminders.tags',
            'reminders.details'
        );

        $deal->orderBy('notify', 'desc')
            ->orderBy('urgent', 'desc')
            ->orderByRaw('ISNULL(duedate)')
            ->orderBy('duedate');

        return $deal->get();
    }

    public static function getDealById($dealId)
    {
        return Deal::where('id', '=', $dealId)
            ->whereIn('user_id', UserUtil::getBrockerIds())
            ->get();
    }

    public static function getDealByPId($deal)
    {
        $parent =
            ($deal->parent_id !== null && $deal->parent_id !== '') ?
                Deal::where('id', '=', $deal->parent_id)->first()
                :
                null;
        return $parent;
    }

    public static function getContentTags()
    {
        return ContentTag::get();
    }

    public static function getContactTypes()
    {
        return ContactType::orderBy('name')
            ->get();
    }

    public static function getDealNotify($dealId)
    {
        return DealNotify::where('deal_id', '=', $dealId)
            ->get();
    }

    public static function getRoles()
    {
        return Role::where('id', '>', 1)
            ->get();
    }

    public static function getDealTree($deal)
    {
        $rootDeal = self::findRootDeal($deal);
        $dealTree = self::buildDealTree($rootDeal);
        return self::_getDealTree($dealTree, $rootDeal);
    }

    public static function _getDealTree($tree, $deal)
    {
        if ($tree->deal->id == $deal->id) {
            $result = '<li data-jstree=\'{ "opened" : true, "selected" : true }\'>';
        } else {
            $result = '<li data-jstree=\'{ "opened" : true }\'>';
        }
        $result .= '<a href="/deal/index/' . $tree->deal->id . '">' . $tree->deal->name . '</a>';
        if (count($tree->children) != 0) {
            $result .= '<ul>';
            foreach ($tree->children as $branch) {
                $result .= self::_getDealTree($branch, $deal);
            }
            $result .= '</ul>';
        }
        $result .= '</li>';
        return $result;
    }


    private static function findRootDeal($deal)
    {
        $parent = self::getDealByPId($deal);
        if ($parent !== null) {
            return self::findRootDeal($parent);
        }
        return $deal;
    }

    private static function buildDealTree($deal)
    {

        $dealTree = new ModelTreeDeal($deal);

        $childDeals = Deal::where('parent_id',$deal->id)
            ->get();

        foreach ($childDeals as $child) {
            $dealTree->addBranch(self::buildDealTree($child));
        }
        return $dealTree;
    }

    public static function getFilesByDealId($dealId)
    {
        return FileManagement::where('deal_id',$dealId)
            ->orderBy('id', 'desc')
            ->get();
    }

    public static function getNotifications($dealId)
    {
        return FileManagement::where('deal_id', $dealId)
            ->orderBy('id', 'desc')
            ->get();
    }

    public static function saveNotification($dealId, $data)
    {
        $newNotification = new DealNotify;
        $newNotification->deal_id = $dealId;
        if(Arr::exists($data, 'for'))
        {
            $newNotification->who_for = implode(',', data_get($data, 'for'));
        }

        $newNotification->save();
        return $newNotification;
    }

    public static function deleteNotification($dealId)
    {
        return DealNotify::where('deal_id', $dealId)
            ->delete();
    }

    public static function getDealContactsByDealId($dealId)
    {
        return DealContact::where('deal_id', $dealId)
            ->get();
    }

    public static function getDealContactByTypeId($contactTypeId)
    {
        return DealContact::join('deals', 'deals.id', '=', 'dealcontacts.deal_id')
            ->where('dealcontacts.contacttype_id', '=', $contactTypeId)
            ->whereIn('deals.user_id', UserUtil::getBrockerIds())
            ->groupBy('dealcontacts.contact_id')
            ->orderBy('deals.created_at', 'DESC')
            ->get();
    }

    public static function getPersonTitles()
    {
        return PersonTitle::orderBy('id')
            ->get();
    }



    public static function getDocumentStatuses()
    {
        return DocumentStatus::orderBy('position')
            ->get();
    }

    public static function createDeal($dealName, $brokerId)
    {
        $_enable = !FormatUtil::checkStringEmpty($dealName);
        if($_enable)
        {
            $newDeal = new Deal;
            $newDeal->name = $dealName;
            $newDeal->status = 2;
            $newDeal->user_id = $brokerId ? $brokerId : Auth::id();
            $newDeal->save();
            return $newDeal;
        }
        else
            return null;
    }

    public static function cloneDeal($dealId, $dealName)
    {
        $_enable = !FormatUtil::checkStringEmpty($dealName);
        if($_enable)
        {
            $deal = Deal::find($dealId);
            $cloneDeal = new Deal;
            $cloneDeal->name = $dealName;
            $cloneDeal->status = $deal->status;
            $cloneDeal->user_id = $deal->user_id;
            $cloneDeal->parent_id = $deal->id;

            $cloneDeal->save();

            $contacts = $deal->contacts;

            foreach($contacts as $contact)
            {
                $newContact = new DealContact;

                $newContact->firstname = $contact->firstname;
                $newContact->middlename = $contact->middlename;
                $newContact->lastname = $contact->lastname;
                $newContact->contacttype_id = $contact->contacttype_id;
                $newContact->persontitle_id = $contact->persontitle_id;
                $newContact->company = $contact->company;
                $newContact->phonemobile = $contact->phonemobile;
                $newContact->phonehome = $contact->phonehome;
                $newContact->phonework = $contact->phonework;
                $newContact->phonefax = $contact->phonefax;
                $newContact->email = $contact->email;
                $newContact->address1 = $contact->address1;
                $newContact->address2 = $contact->address2;
                $newContact->suburb = $contact->suburb;
                $newContact->state = $contact->state;
                $newContact->postcode = $contact->postcode;
                $newContact->notes = $contact->notes;
                $newContact->contact_id = $contact->contact_id;

                $newContact->deal_id = $cloneDeal->id;
                $newContact->save();
            }

            return $cloneDeal->id;
        }
        else
            return null;
    }

    public static function updateDealByField($dealId, $fieldName, $value)
    {
        $_enable = true; //!FormatUtil::checkStringEmpty($dealName);
        if($_enable)
        {
            $updateDeal = Deal::find($dealId);
            if($updateDeal)
            {
                switch($fieldName)
                {
                    case 'name':
                        $updateDeal->name = $value;
                        break;
                    case 'status':
                        $updateDeal->status = $value;
                        break;
                    case 'notes':
                        $updateDeal->notes = $value;
                        break;
                }
                $updateDeal->save();
            }
            return $updateDeal;
        }
        else
            return null;
    }



    public static function getLeadCount($dateFrom, $dateTo)
    {
        return Deal::whereIn('user_id', UserUtil::getBrockerIds())
            ->where('created_at', '>=', $dateFrom)
            ->where('created_at', '<', $dateTo)
            ->get()
            ->count();
    }

    public static function filterQuery($deals, $data)
    {
        if(Arr::exists($data, 'fromdate'))
            $deals->where('deals.created_at', '>=', date('Y-m-d 00:00:00', strtotime(data_get($data, 'fromdate'))));

        if(Arr::exists($data, 'todate'))
            $deals->where('deals.created_at', '<=', date('Y-m-d 23:59:59', strtotime(data_get($data, 'todate'))));

        Arr::exists($data, 'for') && data_get($data, 'for') != null &&
            $deals->whereIn('referrerContact.contact_id', explode(',', data_get($data, 'for')));

        // if(Arr::exists($data, 'order'))
        //     $deals->where('deals.created_at', '<=', date('Y-m-d 23:59:59', strtotime(data_get($data, 'todate'))));

        return $deals;
    }

    public static function getLeadsForTables($data)
    {
        $deals = Deal::select(
                'deals.*',
                'deals.created_at AS received_date',
                'deals.name AS lead_name',
                DB::raw('CONCAT(applicant.phonehome, ", ", applicant.phonemobile, ", ", applicant.phonework) AS contact_number'),
                'applicant.email AS email',
                'deals.notes AS notes',
                DB::raw('CONCAT(referrerContact.firstname, " ", referrerContact.lastname) AS referrer'),
                'dealstatuses.description AS status_description'
            )
            ->leftJoin('dealcontacts as applicant', 'applicant.deal_id', '=', 'deals.id')
            ->leftJoin('dealcontacts as referrerContact','referrerContact.deal_id', '=', 'deals.id')
            ->join('dealstatuses', 'deals.status', '=', 'dealstatuses.id')
            ->whereIn('deals.user_id', UserUtil::getBrockerIds())
            ->where('applicant.contacttype_id', '=', '1')
            ->where('referrerContact.contacttype_id', '=', '3')
            ->groupBy('deals.id');

        $deals = self::filterQuery($deals, $data);

        $columns = array(
            'deals.created_at',
            'deals.name',
            array(
                'contacts.phonemobile',
                'contacts.phonehome',
                'contacts.phonework'
            ),
            'contacts.email',
            'deals.notes',
            array(
                'contacts.firstname',
                'contacts.lastname'
            ),
            'dealstatuses.description',
        );
        $dealTable =  CommonDbUtil::getDataTable($deals, $data, $columns);

        return $dealTable;
    }

    public static function getLeadsForCsv($data)
    {
        $deals = Deal::select(
                'deals.*',
                'deals.created_at AS received_date',
                'deals.name AS lead_name',
                DB::raw('CONCAT(applicant.phonehome, ", ", applicant.phonemobile, ", ", applicant.phonework) AS contact_number'),
                'applicant.email AS email',
                'deals.notes AS notes',
                DB::raw('CONCAT(referrerContact.firstname, " ", referrerContact.lastname) AS referrer'),
                'dealstatuses.description AS status_description'
            )
            ->leftJoin('dealcontacts as applicant', 'applicant.deal_id', '=', 'deals.id')
            ->leftJoin('dealcontacts as referrerContact','referrerContact.deal_id', '=', 'deals.id')
            ->join('dealstatuses', 'deals.status', '=', 'dealstatuses.id')
            ->whereIn('deals.user_id', UserUtil::getBrockerIds())
            ->where('applicant.contacttype_id', '=', '1')
            ->where('referrerContact.contacttype_id', '=', '3')
            ->groupBy('deals.id');

        return $deals = self::filterQuery($deals, $data);
    }

    public static function getMarketingWhiteboard($filter)
    {
        $splits = array();
        $data = Deal::join('dealcontacts', 'deals.id', '=', 'dealcontacts.deal_id')
            ->leftJoin('loansplits', 'deals.id', '=', 'loansplits.deal_id')
            ->join('dealstatuses', 'dealstatuses.id', '=', 'deals.status')
            ->whereIn('deals.user_id', UserUtil::getBrockerIds())
            ->where('dealcontacts.contacttype_id', '=', 1);
            //->whereIn('loansplits.lender_id', [5]);


        if(!empty($filter)) {
            foreach ($filter as $key => $fi) {
                if($key == 'lender') {
                    //$fi = array_map(function($r){return "'{$r}'";}, $fi);
                    $data = $data->whereIn('loansplits.lender_id', $fi);
                } else if($key == 'lvr') {
                    foreach ($fi as $f) {
                        if($f == 1) {
                            $data = $data->where('loansplits.lvr', '<', 80);
                        } else if($f == 2){
                            $data = $data->where('loansplits.lvr', '>=', 80)->where('loansplits.lvr', '<=', 90);
                        } else {
                            $data = $data->where('loansplits.lvr', '>=', 90);
                        }
                    }
                        ;
                } else if($key == 'status') {
                    //$fi = array_map(function($r){return "'{$r}'";}, $fi);
                    $data = $data->whereIn('deals.status', $fi);
                }
            }
        }
        $_result = $data
            ->select('deals.*', 'dealcontacts.phonemobile as phone', 'dealcontacts.email', 'dealcontacts.postcode as postal', 'loansplits.submitted', 'loansplits.settled', 'loansplits.lender', 'loansplits.subloan as amount', 'loansplits.lvr', 'loansplits.tags as type', 'dealstatuses.description as status_name')
            ->get();
        //return count($_result);//$data->toSql();
        if($_result) {
            $splits = $_result;
        }
        $result = array();
        foreach($splits as $split) {
            $data = array();
            $data['id'] = $split->id;
            $data['borrower'] = $split->name;
            $data['submitted'] = empty($split->submitted) ? '' : date('d/m/Y', strtotime($split->submitted));
            $data['settled'] = empty($split->settled) ? '' : date('d/m/Y', strtotime($split->settled));
            $data['lender'] = empty($split->lender) ? '' : $split->lender;
            $data['amount'] = empty($split->amount) ? '' : $split->amount;
            $data['lvr'] = empty($split->lvr) ? '' : $split->lvr;
            $data['status'] = empty($split->status_name) ? '' : $split->status_name;
            $data['type'] = empty($split->type) ? '' : $split->type;
            $data['email'] = empty($split->email) ? '' : $split->email;
            $data['phone'] = empty($split->phone) ? '' : $split->phone;
            $data['postal'] = empty($split->postal) ? '' : $split->postal;
            $result[] = $data;
        }
        return $result;
    }

}
