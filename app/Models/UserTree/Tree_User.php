<?php 
namespace App\Models\UserTree;
use App\Models\UserTree\Base;
class Tree_User extends Base {
    
    public $username;
    public $full_name;
    public $firstname;
    public $lastname;
    
    public $email;
    public $phone_mobile;
    public $phone_office;
    
    public $avatar;
            
    public $login_count;
    public $last_login;
    
    public $is_broker;
    public $is_assisstant;
            
    public static function from_orm($orm)
    {
        $obj = parent::from_ormobj($orm, new Tree_User());
        
        $obj->username = $orm->username;
        $obj->full_name = $orm->lastname  . ', ' . $orm->firstname;
        $obj->firstname = $orm->firstname;
        $obj->lastname = $orm->lastname;
        
        $obj->email = $orm->email;
        $obj->phone_mobile = $orm->phone_mobile;
        $obj->phone_office = $orm->phone_office;
        
        $obj->avatar = $orm->avatar;
        $obj->login_count = $orm->logins;
        $obj->last_login = $orm->last_login;
        
        $obj->is_broker = $orm->isBroker();
        $obj->is_assisstant = $orm->isAssistant();
        
        return $obj;
    }
}