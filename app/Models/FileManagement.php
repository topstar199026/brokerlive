<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FileManagement extends Model
{
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
    protected $table = 'file_management';
}
