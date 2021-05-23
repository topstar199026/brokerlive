<?php

namespace App\Http\Controllers\Util;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use App\Models\User;
use App\Models\Role;
use App\Models\RoleUser;
use App\Models\Brokerassistant;

use App\Datas\UserRole;
//---------Aleksey
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use DB;
use App\Models\FileManagement;
use App\Models\Preference;
use App\Models\BrokerCode;
use App\Models\Lender;
use App\Models\Aggregator;
use App\Models\Organisation;
use App\Models\UserRelation;
use App\Http\Controllers\Util\CommonDbUtil;
use Illuminate\Support\Facades\Storage;
use App\Models\UserTree\Tree_Organisation;
use App\Models\UserTree\Tree_User;
use App\Models\DuplicateAccountAudit;
//----------end
class UserUtil extends Controller
{
    public static function login()
    {
        if(Auth::check())
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public static function checkApiKey(){
        if(self::login())
        {
            $user = User::find(Auth::id());
            if($user->remember_token==''){
                $user->remember_token=Str::random(60);
                $user->save();
                auth()->login($user);
            }
        }
    }
    public static function getUserInfo()
    {
        if(self::login())
        {
            return Auth::user();
        }
        else
        {
            return null;
        }
    }

    public static function getUserDataByEmail($email)
    {
        return User::where('email', $email)
            ->select('id as userId', 'email as userEmail', 'username as userName', 'firstname as firstName', 'lastname as lastName')
            ->first();
    }

    public static function getUserById($id)
    {
        return User::find($id);
    }

    public static function getBrokers()
    {
        $broker = array();
        $assistantBroker = array();
        $role = Auth::user()->getUserRole();
        if ($role->broker) {
            $broker[] = self::getUserInfo();
        }
        if ($role->personalAssistant) {
            $id = Auth::id();
            $assistantBroker =
                Brokerassistant::where('assistant_id', $id)
                ->leftJoin('users', 'users.id', '=', 'user_id')
                ->get()
                ->toArray();

        }
        return array_merge($broker, $assistantBroker);
    }

    public static function getBrockerIds()
    {
        $brokerIds = array();
        $brokers = self::getBrokers();
        foreach ($brokers as $broker) {
            $brokerIds[] = $broker['id'];
        }
        if (empty($brokerIds)) {
            $brokerIds[] = -1;
        }
        return $brokerIds;
    }

    public static function getAssistants($userId)
    {
        $user = User::find($userId);
        if($user->isBroker())
            return User::leftJoin('brokerassistants', 'brokerassistants.assistant_id', '=', 'users.id')
                ->where('brokerassistants.user_id', '=', $userId)
                ->select('users.*')
                ->get()->toArray();
        else
            return array();

    }

    public static function getTeams()
    {
        $team = array(Auth::user());
        if(Auth::user()->isBroker())
            $team = array_merge($team, self::getAssistants(Auth::id()));
        else{
            $brokers = self::getBrokers();
            $team = array_merge($team, $brokers);
            foreach ($brokers as $broker) {
                $team = array_merge($team, self::getAssistants($broker['id']));
            }
        }
        return self::sortCleanTeam($team);
    }

    public static function sortCleanTeam($team)
    {
        $team = array_unique($team, SORT_REGULAR);
        usort($team, function ($a, $b) {
            return strcmp($a['lastname'], $b['lastname']);
        });
        return $team;
    }

    public static function getUsersByType($relationId, $typeId)
    {
        return User::join('user_relation', 'user_relation.user_id', '=', 'users.id')
            ->where('user_relation.relation_id', '=', $relationId)
            ->where('user_relation.type', '=', $typeId)
            ->select('users.*')
            ->get();
    }


    /**-------------------------------------Aleksey-------------------------------------- */
    /**
     * save user info in configuration - profile
     * created on 07/06/2020 by Aleksey
     */
    public static function saveUserProfile(Request $request){
        $id = Auth::id();
        $user = User::find($id);
        $user->firstname = $request->input('firstname');
        $user->lastname = $request->input('lastname');
        $user->email = $request->input('email');
        $user->phone_office = $request->input('phone_office');
        $user->phone_mobile = $request->input('phone_mobile');
        if($user->save()){
            return json_encode(array('status'=>'SUCCESS','msg'=>'Profile updated successfully.'));
        }else{
            return json_encode(array('status'=>'UNSUCCESS','msg'=>'some error !'));
        }
    }

    /**
     * save user info in configuration - passwordChange
     * created on 07/012/2020 by Aleksey
     */
    public static function passwordChange(Request $request){
        $id = Auth::id();
        $user = User::find($id);
        $password = $request->input('password');
        $new_password = Hash::make($request->input('newpassword'));
        $cur_password= $user->password;
        if(Hash::check($password, $cur_password))
        {
          $user->password=$new_password;
          $user->save();
          return json_encode(array('status'=>'SUCCESS','msg'=>'Password updated.'));
        }else
        {
           return json_encode(array('status'=>'UNSUCCESS','msg'=>'Current Password mismatch'));
        }
    }

    public static function passwordUpdate(Request $request){
        $id = $request->input('id');
        $user = User::find($id);
        $new_password = Hash::make($request->input('password'));
          $user->password=$new_password;
          $user->save();
          return json_encode(array('status'=>'SUCCESS','msg'=>'Password updated.','id'=>$id,'newpassword'=>$new_password));
    }

    public static function saveUser($id,$request){
        $user=$id?User::find($id):new User;
        if(!$id&&count(User::where('email','=',$request->input('email'))->get()))return array('email'=>"unique_email");
        if(!$id&&count(User::where('username','=',$request->input('username'))->get()))return array('username'=>"unique_username");
        if(!$id&&$request->input('password')!=$request->input('password_confirm'))return array('password'=>"no match password");
        $column = array('firstname', 'lastname', 'is_broker_admin', 'username', 'email', 'phone_office', 'phone_mobile');
        foreach($column as $col)$user->$col=$request->input($col);
        if(!$id){
            $user->password=Hash::make($request->input('password'));
            $user->created_by=Auth::id();
            $user->created_at=date("Y-m-d H:i:s");
        }
        $user->updated_by=Auth::id();
        $user->updated_at=date("Y-m-d H:i:s");
        $user->save();

        $id=$user->id;
        if($id){
            $roles=$request->input('roles');
            $role_ids=array();
            if (is_array($roles)) {
                foreach ($roles as $value) {
                    $rows=Role::select('id')->where('name','=',$value)->get();
                    if(count($rows))$role_ids[]=$rows[0]['id'];
                }
            }
            $user->setUserRoles($role_ids);

            $relations = array(1 => "Aggregator", 2 => "Head Broker", 3 => "Organisation");
            UserRelation::select()->where('user_id','=',$id)->delete();
            foreach($relations as $key=>$relation)
            if(!empty($request->input('relation_'.$key))){
                $row=new UserRelation;
                $row->user_id=$id;
                $row->relation_id=$request->input('relation_'.$key);
                $row->type=$key;
                $row->name=$relation;
                $row->created_by=Auth::id();
                $row->created_at=date("Y-m-d H:i:s");
                $row->updated_by=$row->created_by;
                $row->updated_at=$row->created_at;
                $row->save();
            }
        }

        return array('id'=>$id);
    }

    public static function getRelations($id){
        $rows = User::select('id','username','email')->get();
        foreach($rows as $row)if($row->getUserRole()->headBroker)$headerBroker[]=$row;
        $listAggregator = Aggregator::select()->orderBy("name", "asc")->get();
        $listOrganisation = Organisation::select()->get();
        $userRelation = UserRelation::select()->where("user_id", "=", $id)->get();
        $relations = array(1 => "Aggregator", 2 => "Head Broker", 3 => "Organisation");
        $binding = array(
            'relations' => $relations,
            'listBroker' => $headerBroker,
            'listAggregator' => $listAggregator,
            'listOrganisation' => $listOrganisation,
        );
        foreach ($userRelation as $key => $relation) {
            $binding["user_" . $relation->type] = $relation->relation_id;
        }
        return $binding;
    }

    /**
     * get all assistance users list by administrator.
     * this will need administrator middleware.
     * Created on 07/06/2020 by Aleksey
     */
    public static function getAssistantList(){
        $id = Auth::id();
        $user = User::find($id);
        $role = Auth::user()->getUserRole();
        $result='';
        if ($role->broker)
        {
            $result = User::select('users.*', 'brokerassistants.id as ass_id')
                ->leftJoin('brokerassistants','users.id', '=', 'brokerassistants.assistant_id')
                ->where('brokerassistants.user_id', '=', $id)
                ->get();
        }else if($role->personalAssistant){
            $result = User::select('users.*', 'brokerassistants.id as ass_id')
                ->leftJoin('brokerassistants','users.id', '=', 'brokerassistants.user_id')
                ->where('brokerassistants.assistant_id', '=', $id)
                ->get();
        }

        if($result)
        {
            foreach($result as $value)
            {
                $result = Role::select('roles.name')
                  ->leftJoin('roles_users','roles.id', '=', 'roles_users.role_id')
                  ->where('roles_users.user_id', '=',$value->id)
                  ->get()
                  ->toArray();
                if($result)
                {
                    $role_name=$result[0]['name'];
                }
                else
                    $role_name='';
                $data['data'][]=array(
                    'id'=>$value->id,
                    'username'=>$value->username,
                    'firstname'=>$value->firstname,
                    'lastname'=>$value->lastname,
                    'email'=>$value->email,
                    'phone_mobile'=>$value->phone_mobile,
                    'phone_office'=>$value->phone_office,
                    'avatar'=>$value->avatar,
                    'role_name'=>$role_name,
                    'ass_id'=>$value->ass_id,
                    'is_broker'=>$role->broker
                );
            }
        }else{
            $data['data'][]=array(
                'id'=>'',
                'username'=>'',
                'firstname'=>'',
                'lastname'=>'',
                'email'=>'',
                'phone_mobile'=>'',
                'phone_office'=>'',
                'avatar'=>'',
                'role_name'=>'',
                'ass_id'=>''
            );
        }
        echo json_encode($data);
        exit();
    }

    /**
     * delete assistance user by administrator.
     * this will need administrator middleware.
     * Created on 07/08/2020 by Aleksey
     */
    public static function deleteAssistant(Request $request){
        return Brokerassistant::find($request->input('id'))->delete();
    }

    /**
     * add assistance user by administrator.
     * this will need administrator middleware.
     * Created on 07/08/2020 by Aleksey
     */
    public static function addAssistant(Request $request){
        $row=new Brokerassistant;
        $row->user_id=Auth::id();
        $row->assistant_id=$request->input('ass_id');
        $row->created_at=date("Y-m-d H:i:s");
        $row->created_by=Auth::id();
        $row->updated_at=$row->created_at;
        $row->updated_by=$row->created_by;
        return $row->save() ? json_encode(array('status'=>'SUCCESS')) : json_encode(array('status'=>'UNSUCCESS'));
    }

    /**
     * find user for assistance by keyword in profile page
     * this will need administrator middleware.
     * Created on 07/08/2020 by Aleksey
     */
    public static function userautocomplete(Request $request){
        return User::select()->where('id','!=',Auth::id())->where('firstname','like',"%{$request->input('data')}%")->get();
    }

    /**
     * get a user in profile page
     * Created on 07/08/2020 by Aleksey
     */
    public static function userdetails(Request $request){
        return User::find($request->input('selected_id'));
    }

    /**
     * get _url: "/data/v1/user",
     * Created on 07/10/2020 by Aleksey
     */
    public static function userlist(Request $request){
        $draw = $request->input('draw');
        $start = $request->query("start");
        $length = $request->query("length");
        $search="";
        $searchParam = $request->query("search");
        if (is_array($searchParam)) {
            $search = $searchParam['value'];
        }
        $order = $request->query('order');
        $query =
                User::select('id','username','firstname','email','logins as login_count')
                ->where("id",'!=',Auth::id());
        $totalData = $query->get()->count();
        $columns = array();
        $query=CommonDbUtil::searchHelper($query,$search,$columns);

        if (is_array($order)) {
            foreach ($order as $col) {
                $colNum = $col['column'];
                $dir = $col['dir'];
                $query=CommonDbUtil::orderHelper($query, $columns[$colNum], $dir);
            }
        }

        $totalFiltered = $query->get()->count();
        if($length>0)$query=$query->limit($length);
        if($start>0)$query=$query->offset($start);
        $data = $query->get();
        for($i=0;$i<count($data);$i++){
            $data[$i]->role=User::find($data[$i]->id)->getUserRole();
        }
        $json_data = array(
            "draw"            => intval( $draw ),
            "recordsTotal"    => intval( $totalData ),
            "recordsFiltered" => intval( $totalFiltered ),
            "data"            => $data
        );
        return $json_data;
    }

    public static function getUser($id){
        if($id){
            $user = User::find($id);
        }else{
            $user = new User;
        }
        $user->role=$user->getUserRole();
        return $user;
    }
    public static function getRoles(){
        return Role::select()->get();
    }

    public static function userTreelist($data=array(),$tree=array()){
        $root_orgs = Organisation::select()
            ->where('parent', '=', NULL)
            ->get();

        $tree = array();
        foreach( $root_orgs as $root_org)
        {
            $tree[] = self::build_organisation($root_org);
        }

        return $tree;
    }
    public static function build_organisation($organisation){
        $org = Tree_Organisation::from_orm($organisation);
        //echo ($organisation->id).',';
        $child_users = User::select('users.*')
            ->join('user_relation','users.id', '=', 'user_relation.user_id')
            ->where('user_relation.type', '=', 3)
            ->where('user_relation.relation_id', '=', $organisation->id)
            ->get();
        foreach ( $child_users as $user ) {
            $org->children[] = Tree_User::from_orm($user);
        }
        $child_orgs = Organisation::select()
            ->where('parent', '=', $organisation->id)
            ->get();

        foreach ( $child_orgs as $child_org ) {
            $org->children[] = self::build_organisation($child_org);
        }

        return $org;
    }

    /**
     * get userfiles in configuration/profile page.
     * this will need administrator middleware.
     * Created on 07/07/2020 by Aleksey
     */
    public static function userfiles(Request $request){
        $draw = $request->input('draw');
        $start = $request->query("start");
        $length = $request->query("length");
        $searchParam = $request->query("search");
        if (is_array($searchParam)) {
            $search = $searchParam['value'];
        }
        $order = $request->query('order');
        $query =
                FileManagement::select('deals.id AS deal_id', 'deals.name AS deal_name',
                                DB::raw('CONCAT(users.firstname,"",users.lastname) AS user_name'),
                                'file_name', 'file_size', 'date','file_management.id as file_id')
                ->join('deals',"deals.id", "=", "file_management.deal_id")
                ->join('users',"file_management.user_id", "=", "users.id")
                ->whereIn("file_management.user_id", UserUtil::getBrockerIds());
        $totalData = $query->get()->count();
        $columns = array("deals.name", "file_name", "file_size",array("users.firstname", "users.lastname"), "date");
        $query=CommonDbUtil::searchHelper($query,$search,$columns);

        if (is_array($order)) {
            foreach ($order as $col) {
                $colNum = $col['column'];
                $dir = $col['dir'];
                $query=CommonDbUtil::orderHelper($query, $columns[$colNum], $dir);
            }
        }

        $totalFiltered = $query->get()->count();

        $data = $query
            ->limit($length)
            ->offset($start)
            ->get();

        $json_data = array(
            "draw"            => intval( $draw ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
            "recordsTotal"    => intval( $totalData ),  // total number of records
            "recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
            "data"            => $data // total data array
        );
        return $json_data;
    }

    /**
    * get updateAvatar in configuration/profile page.
    * this will need administrator middleware.
    * Created on 07/09/2020 by Aleksey
    */
    public static function updateAvatar($file){
        try
        {
            $fileName = Auth::id()."_".time().$file->getClientOriginalName();
            /* Add s3 storage Path*/
            $appPath="/home/brokerli/application/";
            $filePath = $appPath.'files/avatars';
            $filePath = Storage::disk('s3')->putFileAs($filePath, $file, $fileName);
            $user=User::find(Auth::id());
            $user->avatar=$filePath;
            $user->save();
            $file = Storage::disk('s3')->download($filePath);
        }
        catch(Exception $e)
        {

        }
        $result["result"] = 1;
        $result["file"] = '/configuration/profile/getAvatar';
        return json_encode($result);
    }

    /**
    * get Avatar in configuration/profile page.
    * Created on 07/09/2020 by Aleksey
    */
    public static function getAvatar(Request $request){
        $size=$request->input('s');
        $file=Storage::disk('s3')->download(Auth::user()->avatar);
        return $file;
    }

    /**
     * get List of BrokerCode in configuration/profile page
     * //_url: "/user/listBrokerCode",
     * _url: "/configuration/profile/listBrokerCode",
     * Created on 07/07/2020 by Aleksey
     */
    public static function getListBrokerCode(){
        $data = BrokerCode::select('id','lender_id','code','password')
            ->where('broker_id', "=", Auth::id())
            ->orderBy('created_at', "desc")
            ->get();
        $json_data = array(
            "draw"            => 1,
            "recordsTotal"    => count( $data ),
            "recordsFiltered" => count( $data ),
            "data"            => $data
        );
        return $json_data;
    }

    /**
     * update BrokerCode in configuration/profile page
     * //_url: "/user/editBrokerCode",
     * _url: "/configuration/profile/editBrokerCode",
     * Created on 07/08/2020 by Aleksey
     */
    public static function updateBrokerCode(Request $request){
        $id = $request->input('id');
        $data=$id!=''?BrokerCode::find($id):new BrokerCode;
        $data->broker_id = Auth::id();
        $lenderId = $request->input("lender_id");
        if($lenderId !== null) {
            $data->lender_id = $lenderId;
        }
        $code = $request->input("code");
        if($code !== null) {
            $data->code = $code;
        }
        $password = $request->input("password");
        if($password !== null) {
            $data->password = $password;
        }
        $data->save();
        return $data->id;
    }

    /**
     * update BrokerCode in configuration/profile page
     * //_url: /user/preferencePost
     * _url: /configuratioin/profile/preferencePost
     * Created on 07/08/2020 by Aleksey
     */


    public static function preferencePost($data){

        $type = data_get($data, '_type');
        $key = null;
        $value = null;
        if($type == 'BOOLEAN')
        {
            $key =  data_get($data, 'BOOLEAN-key');
            $value =  data_get($data, 'BOOLEAN-value', '') == 'on' ? 'true' : 'false';
        }
        else if($type == 'NUMBER')
        {
            $key =  data_get($data, 'NUMBER-key');
            $value =  data_get($data, 'NUMBER-value');
        }
        else if($type == 'TIME')
        {
            $key =  data_get($data, 'TIME-key');
            $value =  data_get($data, 'TIME-value');
        }

        $preference = Preference::select()
            ->where('name', '=', $key)
            ->where('user_id', '=', Auth::id())
            ->get();

        if ($preference === null||count($preference)==0) {
            $preference = new Preference;
        }else
            $preference=$preference[0];

        $preference->name = $key;
        $preference->user_id = Auth::id();
        $preference->value = $value;
        $preference->save();
        return $preference->id;
    }


    public static function _preferencePost(Request $request){
        $preference = Preference::select()
            ->where('name', '=', $request->input('id'))
            ->where('user_id', '=', Auth::id())
            ->get();

        if ($preference === null||count($preference)==0) {
            $preference = new Preference;
        }else
            $preference=$preference[0];
        $preference->name = $request->input('id');
        $preference->user_id = Auth::id();
        $preference->value = $request->input('value');
        $preference->save();
        return $preference->id;
    }

    /**
     * get List of Leader in configuration/profile page
     * //url: "/user/lenders",
     *    url: "/configuration/profile/getLenders",
     * Created on 07/07/2020 by Aleksey
     */
    public static function getListLender(){
        return Lender::select('id','name')->get();
    }

    public static function getPreference($preference_name)
    {
        $preference = Preference::select()
            ->where('name', '=', $preference_name)
            ->where('user_id', '=', Auth::id())
            ->get();
        if (!empty($preference) && isset($preference[0]->value)){
            return $preference[0]->value;
        }
        return 0;
    }

    public static function getListBroker(){
        $listHeadBroker = User::select('users.*')
        ->leftJoin('roles_users', 'roles_users.user_id','=','users.id')
        ->leftJoin('roles', 'roles.id','=','roles_users.role_id')
        ->where('roles.name', '=','Head Broker')
        ->get();
        return $listHeadBroker;
    }
    public static function getListAggregator(){
        return Aggregator::orderBy('name' , "asc")->get();
    }
    public static function getListOrganisation(){
        return Organisation::select()->get();
    }
    public static function getRolesByUserId($id){
        return RoleUser::select('role_id')->where('user_id','=',$id)->get();
    }
    public static function getRelationByUserId($id){
        return UserRelation::select()->where('user_id','=',$id)->get();
    }
    public static function UserLockout($userId){
        $result = array(
            "result" => 0
        );
        if(Auth::user()->isAdmin()){
            RoleUser::where('user_id', '=', $userId)->where("role_id", "=", 1)->delete();
            $result["result"] = 1;
            $result["userId"] = $userId;
        }
        return $result;
    }
    public static function UserUnlock($userId){
        $result = array(
            "result" => 0
        );
        if(Auth::user()->isAdmin()){
            User::find($userId)->addUserRole(1);
            $result["result"] = 1;
            $result["userId"] = $userId;
        }
        return $result;
    }
    public static function Resetpassword($id,$password){
        $user = User::find($id);
        $user->password = Hash::make($password);
        $user->save();
    }
    public static function CopyDeal(Request $request){
        $result = array();
        $fromUser = $this->request->post("id");
        $mail = $this->request->post("email");
        $reason = $this->request->post("reason");
        $toUser = User::where("email", "=", $mail)->find();
        $fromUser = User::find($fromUser);
        if (empty($toUser) || !$toUser->loaded()) {
            $result["result"] = 0;
            $result["message"] = "Email: {$mail} not exist";
        } else {
            $user=Auth::user();
            $audit = new DuplicateAccountAudit;
            $audit->admin_id = $user->id;
            $audit->admin_email = $user->email;
            $audit->from_user_id = $fromUser->id;
            $audit->from_user_email = $fromUser->email;
            $audit->to_user_id = $toUser->id;
            $audit->to_user_email = $toUser->email;
            $audit->reason = $reason;
            $audit->result = '';

            $audit->save();

            $logDir = APPPATH . "/logs/duplication/";
            if (!is_dir($logDir)) {
                mkdir($logDir, 0744, true);
            }
            $logFile = $logDir . $fromUser->username . "_" . $fromUser->id . "-to-" . $toUser->username . "_" . $toUser->id . "_" . time() . ".log";

            $result["result"] = "";/*Helper_Task::runTask("DuplicateUser", array(
                "env" => Kohana::$environment,
                "from-user-email" => $fromUser->email,
                "to-user-email" => $toUser->email,
                "audit-id" => $audit->id
                //"duplicate-aws-file" => true
            ), "1> $logFile 2>&1", true);*/
        }
        echo json_encode($result);
        exit();
    }
}
