<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use ElasticScoutDriverPlus\CustomSearch;

class JournalEntry extends Model
{
    use Searchable;
    use CustomSearch;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'journalentries';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    protected $dates = ['entrydate'];

    public function _entrydate()
    {
        return $this->entrydate ? date('d M Y', strtotime($this->entrydate)) : '';
    }

    /**
     * [createdBy description]
     * @return [type] [description]
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    /**
     * [updatedBy description]
     * @return [type] [description]
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    public static function add_entry($entry)
    {
        $journal = null;
        if (isset($entry["deal_id"]) && isset($entry["user_id"]) && isset($entry["username"])) {
            try {
                $entry["entrydate"] = date("Y-m-d H:i:s");
                $journal = JournalEntry::table('journalentries');
                foreach ($entry as $key => $value) {
                    $journal->{$key} = $value;
                }
                $journal->save();

                $deal = DB::table("deals")->where('id', $entry["deal_id"]);
                $deal->last_journal_activity = $entry["entrydate"];
                $deal->save();
            } catch (Exception $e) {
            }
        }
        return $journal;
    }

    public function toSearchableArray()
    {
        return [
            'user_id' => $this->user_id,
            'notes' => $this->notes,
            'tags' => $this->tags,
            'deal_id' => $this->deal_id
        ];
    }
}
