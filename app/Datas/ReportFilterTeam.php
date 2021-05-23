<?php

namespace App\Datas;

use App\Http\Controllers\Util\UserUtil;
use App\Http\Controllers\Util\RelationUtil;

use Illuminate\Support\Facades\Auth;
use App\Datas\SelectOptionData;

class ReportFilterTeam
{
    public $user;
    public $fromDate;
    public $toDate;
    public $teams;

    public function __construct($user) {
        $this->user = $user;
        $this->teams = array();
        $this->set_default_team();
    }

    public function organisation_ids() {
        return array_filter(array_map(
            function($value) {
                if ($value['type'] == 'Organisation') {
                    return $value['value'];
                } else {
                    return '';
                }
            },
            $this->teams
        ));
    }

    public function team_brokers() {
        return array_map(
            function($value){
                return $value['id'];
            },
            $this->get_team_user_list()
        );
    }

    private function set_default_team() {
        $relation = RelationUtil::getTeamRelatedOrg();
        $this->teams[] = array(
            'type' => 'Organisation',
            'value' => $relation->relation_id ?? null
        );
    }

    public function get_team_user_list() {
        $all_users = array();
        foreach ($this->teams as $team)
        {
            $users = [];
            if ( $team['type'] == 'HeadBroker' )
            {
                $users = UserUtil::getUsersByType(Auth::id(), 2);
            }else if ( $team['type']  == 'Organisation' )
            {
                $users = UserUtil::getUsersByType($team['value'], 3);
            }
            $all_users = array_merge($all_users, $users->toArray());
        }
        // return $all_users;//error_log($all_users.toString());
        // $all_users = array_unique($all_users);
        return $all_users;
    }
}
