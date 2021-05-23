<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Organisation extends Model
{
    public function children()
    {
        return Organisation::where('parent', '=', $this->id)
            ->get();
    }

    public function tree()
    {
        $orgs = array();
        $owner_parent = $this->owner_parent();
        
        $orgs[] = $owner_parent;
        
        $children = $owner_parent->children();
        if (!empty($children)) {
            $orgs = array_merge($orgs, $children->toArray());
            foreach($children as $child) {
                $orgs = array_merge($orgs, $child->children()->toArray());
            }
        }
        return $orgs;  
    }

    public function owner_parent() {
        // if ($this->parent_organisation->id == '') {
        //     return $this;
        // }
        // return $this->parent_organisation->owner_parent();

        if($this->parent_organisation && $this->parent_organisation->id) return $this->parent_organisation->owner_parent();
        else return $this;
    }

    public function parent_organisation()
    {
        return $this->belongsTo('App\Models\Organisation', 'parent');
    }
}
