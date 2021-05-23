<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use ElasticScoutDriverPlus\CustomSearch;
class Deal extends Model
{
    use Searchable;
    use CustomSearch;

    public function contacts()
    {
        return $this->hasMany('App\Models\DealContact');
    }

    public function broker()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function dealstatus()
    {
        return $this->belongsTo('App\Models\Dealstatus', 'status');
    }


    public function referrer()
    {
        $referrer = DealContact::where('deal_id', '=', $this->id)
            ->where('contacttype_id', '=', 3)
            ->get();

        return $referrer ?? null;
    }

    public function reminder()
    {
        return $this->hasMany('App\Models\Reminder');
    }

    public function firstReminder()
    {
        // if ($this->reminder !== null)
        // {
        //     return $this->reminder;
        // }

        if (isset($this->reminder_id))
        {
            $this->reminder = Reminder::where('id', '=', $this->reminder_id);
        }
        else
        {
            // Get urgent reminders first.
            $reminder = Reminder::where('deal_id', '=', $this->id)
                ->whereNull('completed')
                ->where('tags', 'LIKE', '%Urgent%')
                ->orderBy('duedate', 'asc')
                ->first();
            if ($reminder === null) {
                $reminder = Reminder::where('deal_id', '=', $this->id)
                ->whereNull('completed')
                ->orderBy('duedate', 'asc')
                ->first();
            }
        }
        return $reminder;
    }

    public function toSearchableArray()
    {
        return [
            'user_id' => $this->user_id,
            'name' => $this->name,
            'notes' => $this->notes,
            'status' => $this->status,
            'last_journal_activity' => $this->last_journal_activity
        ];
    }
}
