<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use ElasticScoutDriverPlus\CustomSearch;

class Reminder extends Model
{
    use Searchable;
    use CustomSearch;

    protected $dates = ['duedate', 'starttime'];

    protected $appends = ['user_id'];

    public function getUserIdAttribute()
    {
        return $this->deal->user_id ?? 0;
    }

    public function deal()
    {
        return $this->belongsTo('App\Models\Deal','deal_id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'created_by');
    }

    public function getCreateDate()
    {
        //return date('d M Y, h:i A', $this->created_at);
        return $this->created_at->format('d M Y, h:i A');
    }

    public function getDueDate()
    {
        //return date('d M Y, h:i A', $this->duedate);
        return $this->duedate->format('d M Y, h:i A');
    }

    public function _startTime()
    {
        return $this->starttime? $this->starttime->format('h:i A') : null;
    }

    public function hasTag($tagName)
    {
        $reminderTags = preg_split("/[,]+/", $this->tags);
        return in_array($tagName, $reminderTags) ? 'selected' : '';
    }

    public function arrayTag()
    {
        return explode(',', $this->tags);
    }

    public function hasFor($forName)
    {
        $reminderFors = preg_split("/[,]+/", $this->who_for);
        return in_array($forName, $reminderFors) ? 'selected' : '';
    }

    public function toSearchableArray()
    {
        return [
            'details' => $this->details,
            'tags' => $this->tags,
            'who_for' => $this->who_for,
            'deal_id' => $this->deal_id,
            'completed' => $this->completed,
            'duedate' => $this->duedate,
            'user_id' => $this->user_id
        ];
    }
}
