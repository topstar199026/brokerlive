<?php 
namespace App\Models\UserTree;
class Base {
    
    public $id;
    public $stamp_created;
    public $stamp_updated;
    
    protected static function from_ormobj($orm, $obj)
    {
        $obj->id = $orm->id;
        $obj->stamp_updated = $orm->stamp_updated;
        $obj->stamp_created = $orm->stamp_created;
        return $obj;
    }
    
    public static function from_array($orm_array)
    {
        $objs = array();
        foreach ($orm_array as $orm)
        {
            $objs[] = static::from_orm($orm);
        }
        return $objs;
    }
}