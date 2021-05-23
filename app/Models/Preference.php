<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Preference extends Model
{
    public function preference_user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }    
}
