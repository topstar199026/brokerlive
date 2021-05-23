<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Util\UserUtil;
use App\Http\Controllers\Util\AggregatorUtil;
use App\Http\Controllers\Util\ProcessUtil;
use App\Http\Controllers\Util\OrganisationUtil;
use App\Http\Controllers\Util\SystemTasksUtil;
use App\Http\Controllers\Util\PreferenceUtil;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Arr;

/**
 * ConfigurationController
 * This controller is for all pages in configuration menu
 * created by Aleksey on 7/6/2020
 */
class ConfigurationController extends Controller
{
    public $relations = array(1 => "Aggregator", 2 => "Head Broker", 3 => "Organisation");
    /**
     * This action is assigned /configuration
     * thus, index action is defauly root route in this controller
     */
    public function index(Request $request)
    {
        return $this::profile($request);
    }


    public function profile(Request $request){
        $action = $request->route('action');
        $actionId = $request->route('id');
        switch ($action)
        {
            case 'updateProfileDetails':
                return UserUtil::saveUserProfile($request);
            case 'getAvatar':
                return UserUtil::getAvatar($request);
            case 'updateAvatar':
                return $request->hasfile('file') ? UserUtil::updateAvatar($request->file('file')):null;
            case 'getAssistantList':
                return UserUtil::getAssistantList();
            case 'addAssistant':
                return UserUtil::addAssistant($request);
            case 'deleteAssistant':
                return UserUtil::deleteAssistant($request);
            case 'userautocomplete':
                return UserUtil::userautocomplete($request);
            case 'userdetails':
                return UserUtil::userdetails($request);
            case 'getLenders':
                return UserUtil::getListLender();
            case 'listBrokerCode':
                return UserUtil::getListBrokerCode();
            case 'editBrokerCode':
                return UserUtil::updateBrokerCode($request);
            case 'preferencePost':
                $data = Arr::except($request->all(), ['_token']);
                UserUtil::preferencePost($data);
                return redirect()->intended('/configuration/profile');
            default:
                $preferenceList = PreferenceUtil::getPreferenceList();
                return view('pages.configuration.profile',[
                    'title' => "My Profile",
                    'layout' => "double",
                    'userCommissionValue' => UserUtil::getPreference('commission_value'),
                    'preferenceList' => $preferenceList
                    ]
                );
        }
    }
    public function userfiles(Request $request){
        return UserUtil::userfiles($request);
    }
    public function user(Request $request){
        return view('pages.configuration.user',[
            'title' => "Users",
            'layout' => "simple",
            ]
        );
    }

    public function userlist(Request $request){
        return UserUtil::userlist($request);
    }

    public function userTreelist(){
        return UserUtil::userTreelist();
    }

    public function createUser(Request $request){
        $user=UserUtil::getUser(0);
        $errors=array();
        if($request->method()=='POST'){
            $errors=UserUtil::saveUser(0,$request);
            return json_encode($errors);
        }
        $listBroker = UserUtil::getListBroker();
        $listAggregator = UserUtil::getListAggregator();
        $listOrganisation = UserUtil::getListOrganisation();
        $listRole = UserUtil::getRoles();
        $render=array(
            'title' => "Create User",
            'layout' => "simple",
                'user' => $user,
                'relations' => $this->relations,
                'listBroker' => $listBroker,
                'listAggregator' => $listAggregator,
                'listOrganisation' => $listOrganisation,
                'roles' => $listRole,
                'errors' => $errors,
        );
        return view('pages.configuration.userform',$render);
    }
    public function editUser(Request $request){
        $id=$request->route('id');
        if($request->method()=='POST'){
            UserUtil::saveUser($id,$request);
        }
        $user=UserUtil::getUser($id);
        $role = UserUtil::getRolesByUserId($id);
        $listBroker = UserUtil::getListBroker();
        $listAggregator = UserUtil::getListAggregator();
        $listOrganisation = UserUtil::getListOrganisation();
        $listRole = UserUtil::getRoles();
        $render=array(
            'title' => "Create User",
            'layout' => "simple",
                'user' => $user,
                'relations' => $this->relations,
                'listBroker' => $listBroker,
                'listAggregator' => $listAggregator,
                'listOrganisation' => $listOrganisation,
                'roles' => $listRole,
                'role_id' => $role,
                'errors' => '',
        );
        $userRelation = UserUtil::getRelationByUserId($id);
        foreach ($userRelation as $key => $relation) {
            $render["user_" . $relation->type] = $relation->relation_id;
        }
        return view('pages.configuration.userform',$render);
    }
    public function UserLockout(Request $request) {
        return json_encode(UserUtil::UserLockout($request->input('userId')));
    }
    public function UserUnlock(Request $request) {
        return json_encode(UserUtil::UserUnlock($request->input('userId')));
    }
    public function Resetpassword(Request $request){
        UserUtil::Resetpassword($request->input('id'),$request->input('password'));
        return redirect()->intended('/configuration/user/edit/'.$request->input('id'));
    }
    public function CopyDeal(Request $request){
        return UserUtil::CopyDeal($request);
    }


    public function editUserOld(Request $request){
        $id=$request->route('id');
        if($request->method()=='POST'){
            UserUtil::saveUser($id,$request);
        }
        $binding = UserUtil::getRelations($id);
        $render=array(
            'title' => "Users",
            'layout' => "double",
            'message' => '',
            'user_details' => UserUtil::getUser($id),
            'roles' => UserUtil::getRoles(),
            'relationship' => 'N',
        );
        foreach ($binding as $key => $val) {
            $render[$key] = $val;
        }
        return view('pages.configuration.useredit',$render);
    }

    public function changepassword(Request $request){
        return view('pages.configuration.changepassword',[
            'title' => "Change password",
            'errors' => "",
            'layout' => "double",
            ]
        );
    }

    public function passwordChangeSuccess(Request $request){
        return UserUtil::passwordChange($request);
    }
    public function updatePassword(Request $request){
        UserUtil::passwordUpdate($request);
        $id = $request->input('id');
        return redirect()->intended('/configuration/user/edit/'.$id);
    }

    public function aggregator(Request $request){
        return view('pages.configuration.aggregator',[
            'title' => "Aggregator",
            'layout' => "simple",
            ]
        );
    }
    public function getAggregator(Request $request){
        return AggregatorUtil::getAggregator($request);
    }
    public function createAggregator(Request $request){
        if($request->method()=='POST'){
            AggregatorUtil::saveAggregator($request);
            return view('pages.configuration.aggregator',[
                'title' => "Aggregator",
                'layout' => "simple",
                ]
            );
        }
        return view('pages.configuration.formaggregator',[
            'title' => "Aggregator",
            'errors' => "",
            'layout' => "simple",
            ]
        );
    }
    public function editAggregator(Request $request){
        $id=$request->route('id');
        if($request->method()=='POST'){
            AggregatorUtil::editAggregator($id,$request);
            return view('pages.configuration.aggregator',[
                'title' => "Aggregator",
                'layout' => "simple",
                ]
            );
        }
        return view('pages.configuration.formaggregator',[
            'title' => "Aggregator",
            'errors' => "",
            'layout' => "simple",
            'aggregator'=>AggregatorUtil::getAggregatorById($id)
            ]
        );
    }

    //configuration -> process page
    public function process(Request $request){
        return view('pages.configuration.process',[
            'title' => "Process",
            'layout' => "simple",
            ]
        );
    }
    public function getProcess(Request $request){
        return ProcessUtil::getProcess($request);
    }
    public function createProcess(Request $request){
        if($request->method()=='POST'){
            ProcessUtil::saveProcess($request);
        }
        return view('pages.configuration.formprocess',[
            'title' => "Process",
            'layout' => "simple",
            'dealStatus' => ProcessUtil::getDealstatus(),
            'errors' => ''
            ]
        );
    }
    public function editProcess(Request $request){
        $id=$request->route('id');
        $errors="";
        if($request->method()=='POST'){
            ProcessUtil::editProcess($id,$request);
            $errors="Updated successfully.";
        }
        return view('pages.configuration.formprocess',[
            'title' => "Process",
            'errors' => $errors,
            'layout' => "simple",
            'process' => ProcessUtil::getProcessById($id),
            'dealStatus' => ProcessUtil::getDealstatus(),
            'sections' => ProcessUtil::getSections($id),
            'tasks' => ProcessUtil::getTasks($id),
            ]
        );
    }

    //configuration -> ogranisation page
    public function organisation(Request $request){
        return view('pages.configuration.organisation',[
            'title' => "Organisation",
            'layout' => "simple",
            'error' => "",
            'error_type' => "",
            ]
        );
    }
    public function getOrganisation(Request $request){
        return OrganisationUtil::getOrganisation($request);
    }
    public function createOrganisation(Request $request){
        if($request->method()=='POST'){
            return view('pages.configuration.organisation',[
                'title' => "Organisation",
                'layout' => "simple",
                'error' => 'Saved successfully.',
                'error_type' => "success",
                'organisation' => OrganisationUtil::saveOrganisation($request),
                ]
            );
        }
        return view('pages.configuration.formorganisation',[
            'title' => "Organisation",
            'layout' => "simple",
            'listOrganisation' => OrganisationUtil::getOrganisationList(),
            'errors' => ''
            ]
        );
    }

    public function editOrganisation(Request $request){
        $id=$request->route('id');
        $errors="";
        if($request->method()=='POST'){
            ProcessUtil::editOrganisation($id,$request);
            $errors="Updated successfully.";
        }
        return view('pages.configuration.formorganisation',[
            'title' => "Organisation",
            'layout' => "simple",
            'listOrganisation' => OrganisationUtil::getOrganisationList(),
            'organisation' => OrganisationUtil::getOrganisationById($id),
            'errors' => $errors,
            'error_type' => "success",
            ]
        );
    }

    private function build_pagination($properties)
    {
        return Pagination::factory(array
        (
            'style' => 'digg',
            'items_per_page' => $properties['page_limit'],
            'query_string' => 'page',
            'total_items' => $properties['count']
        ));
    }

    public function systemTasks(Request $request){
        return view('pages.configuration.systemtasks',[
            'title' => "System Tasks",
            'layout' => "simple",
            'error' => "",
            'error_type' => "",
            ]
        );
    }

    public function getTask(Request $request){
        return SystemTasksUtil::getSystemTasks();
    }

    public function saveTask(Request $request){
        return SystemTasksUtil::runTask($request->input('taskName'),$request->input('taskParameter'));
    }
}
