<?php
namespace App\Models\UserTree;
use App\Models\UserTree\Base;
class Tree_Organisation extends Base{
    
    public $full_name;

    public $legal_name;
    public $trading_name;
    public $short_name;
    public $acn;
    public $address_line1;
    public $address_line2;
    public $suburb;
    public $state;
    public $postcode;
    public $country;
    public $phone_number;
    public $fax_number;
    public $parent;
    public $stamp_created;
    public $stamp_updated;

    public $children;
    
    public static function from_orm($orm)
    {
        $obj = parent::from_ormobj($orm, new Tree_Organisation());
        
        $obj->full_name = ( $orm->legal_name != '' ? $orm->legal_name : $orm->trading_name );

        $obj->legal_name = $orm->legal_name;
        $obj->trading_name = $orm->trading_name;
        $obj->short_name = $orm->short_name;
        $obj->acn = $orm->acn;
        $obj->address_line1 = $orm->address_line1;
        $obj->address_line2 = $orm->address_line2;
        $obj->suburb = $orm->suburb;
        $obj->state = $orm->state;
        $obj->postcode = $orm->postcode;
        $obj->country = $orm->country;
        $obj->phone_number = $orm->phone_number;
        $obj->fax_number = $orm->fax_number;
        $obj->parent = $orm->parent;
        $obj->stamp_created = $orm->stamp_created;
        $obj->stamp_updated = $orm->stamp_updated;

        $obj->children = array();
        
        return $obj;
    }
}