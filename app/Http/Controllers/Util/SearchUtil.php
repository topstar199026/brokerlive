<?php

namespace App\Http\Controllers\Util;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;


use App\Http\Controllers\Util\SystemTasksUtil;
use App\Http\Controllers\Util\UserUtil;

use Illuminate\Support\Arr;

use App\Models\Deal;
use App\Models\DealContact;
use App\Models\Reminder;
use App\Models\JournalEntry;

class SearchUtil extends Controller
{
    public static function searchByPhone($phone)
    {
        // $searchResult = DealContact::rawSearch()
        //     ->query(['wildcard' => ['phone' => '*'.$phone.'*']])
        //     ->source(['deal_id'])
        //     ->from(0)
        //     ->size(10000)
        //     ->raw();

        $searchResult = DealContact::rawSearch()
            ->query([
                'bool' => [
                    'must' => [
                        ['terms' => ['user_id' => UserUtil::getBrockerIds()]],
                        ['wildcard' => ['phone' => '*'.$phone.'*']]
                    ]
                ]
            ])
            ->source(['deal_id'])
            ->from(0)
            ->size(10000)
            ->raw();

        $results = [];
        if($searchResult['hits']['total']['value'] == 0)
            return [];
        else
            $results = $searchResult['hits']['hits'];

        return Arr::pluck($results, '_source.deal_id');

        // foreach ($results as $result) {
        //     $deals[] = ORM::factory('Deal', $hit->deal_id);
        // }
    }

    public static function searchByName($q)
    {
        // return $searchResult = Deal::rawSearch()
        //     //->query(['query_string' => ['query' => '*'.$q.'*', 'fields' => ['name', 'notes']]])
        //     ->query(['multi_match' => ['query' => $q, 'fields' => ['name', 'notes']]], ['term' => ['user_id' => UserUtil::getBrockerIds()]])
        //     ->source(['user_id', 'name', 'notes'])
        //     ->from(0)
        //     ->size(1000)
        //     ->raw();

        $searchResult = Deal::rawSearch()
            ->query([
                'bool' => [
                    'must' => [
                        ['terms' => ['user_id' => UserUtil::getBrockerIds()]],
                        ['multi_match' => ['query' => $q, 'fields' => ['name', 'notes']]]
                    ]
                ]
            ])
            ->source(['user_id'])
            ->from(0)
            ->size(10000)
            ->raw();


        $results = [];
        if($searchResult['hits']['total']['value'] == 0){}
        else
            $results = $searchResult['hits']['hits'];

        $dealResult = array_map('intval', Arr::pluck($results, '_id'));

            //-----------

        // $searchResult = DealContact::rawSearch()
        // ->query(['query_string' => ['query' => '*'.$q.'*', 'fields' => ['firstname', 'lastname', 'company', 'email', 'address1', 'address2', 'suburb', 'state', 'postcode', 'notes', 'phone']]])
        //     ->source(['deal_id'])
        //     ->from(0)
        //     ->size(1000)
        //     ->raw();
        // $results = [];
        // if($searchResult['hits']['total']['value'] == 0)
        //     return [];
        // else
        //     $results = $searchResult['hits']['hits'];

        $searchResult = DealContact::rawSearch()
            ->query([
                'bool' => [
                    'must' => [
                        ['terms' => ['user_id' => UserUtil::getBrockerIds()]],
                        ['multi_match' => ['query' => $q, 'fields' => ['firstname', 'lastname', 'company', 'email', 'address1', 'address2', 'suburb', 'state', 'postcode', 'notes', 'phone']]]
                    ]
                ]
            ])
            ->source(['deal_id', 'phone'])
            ->from(0)
            ->size(10000)
            ->raw();
        $results = [];
        if($searchResult['hits']['total']['value'] == 0){}
        else
            $results = $searchResult['hits']['hits'];


        $dealcontactResult = Arr::pluck($results, '_source.deal_id');

        //--------------

        // $searchResult = Reminder::rawSearch()
        //     ->query(['query_string' => ['query' => '*'.$q.'*', 'fields' => ['details', 'tags']]])
        //     ->source(['deal_id','details', 'tags'])
        //     ->from(0)
        //     ->size(1000)
        //     ->raw();
        // $results = [];
        // if($searchResult['hits']['total']['value'] == 0)
        //     return [];
        // else
        //     $results = $searchResult['hits']['hits'];

        $searchResult = Reminder::rawSearch()
            ->query([
                'bool' => [
                    'must' => [
                        ['terms' => ['user_id' => UserUtil::getBrockerIds()]],
                        ['multi_match' => ['query' => $q, 'fields' => ['details', 'tags']]]
                    ]
                ]
            ])
            ->source(['deal_id','details', 'tags'])
            ->from(0)
            ->size(10000)
            ->raw();
        $results = [];
        if($searchResult['hits']['total']['value'] == 0){}
        else
            $results = $searchResult['hits']['hits'];

        $reminderResult = Arr::pluck($results, '_source.deal_id');

        $searchResult = JournalEntry::rawSearch()
            ->query([
                'bool' => [
                    'must' => [
                        ['terms' => ['user_id' => UserUtil::getBrockerIds()]],
                        ['multi_match' => ['query' => $q, 'fields' => ['notes', 'tags']]]
                    ]
                ]
            ])
            ->source(['deal_id','notes', 'tags'])
            ->from(0)
            ->size(10000)
            ->raw();
        $results = [];
        if($searchResult['hits']['total']['value'] == 0){}
        else
            $results = $searchResult['hits']['hits'];

        $journalentryResult = Arr::pluck($results, '_source.deal_id');


        return array_values(Arr::sort(array_values(array_unique(Arr::collapse([$dealResult, $dealcontactResult, $reminderResult, $journalentryResult])))));
    }

    public static function searchDeal($dealIds)
    {
        return Deal::whereIn('id', $dealIds)
            //->whereIn('user_id', UserUtil::getBrockerIds())
            ->orderBy('created_at')
            ->get();
    }

    public static function getState($model)
    {
        switch($model)
        {
            case 'deal':
                return Deal::count();
                break;
            case 'dealcontact':
                return DealContact::count();
                break;
            case 'reminder':
                return Reminder::count();
                break;
            case 'journalentry':
                return JournalEntry::count();
                break;
        }
    }

    public static function getActionKey()
    {
        return env('Action_Key', '&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&');
    }
}
