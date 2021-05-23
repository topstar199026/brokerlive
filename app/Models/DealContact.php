<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use ElasticScoutDriverPlus\CustomSearch;


use App\Http\Controllers\Util\FormatUtil;

class DealContact extends Model
{
    use Searchable;
    use CustomSearch;
    //
    protected $appends = ['phone', 'user_id'];

    public function getPhoneAttribute()
    {
        $phones = array();
        $phones[] = preg_replace('/\s/', '', $this->phonemobile);
        $phones[] = preg_replace('/\s/', '', $this->phonehome);
        $phones[] = preg_replace('/\s/', '', $this->phonework);
        $phones[] = preg_replace('/\s/', '', $this->phonefax);
        $phone = implode(' ', array_filter($phones));

        return $phone;
    }

    public function getUserIdAttribute()
    {
        return $this->deal->user_id ?? 0;
    }

    public function type()
    {
        return $this->belongsTo('App\Models\ContactType','contacttype_id');
    }

    public function deal()
    {
        return $this->belongsTo('App\Models\Deal','deal_id');
    }

    public function fullName()
    {
        return ucfirst($this->firstname).' '.ucfirst($this->lastname);
    }

    public function contactNum()
    {
        return
            !FormatUtil::checkStringEmpty($this->phonemobile) ?
                $this->phonemobile
                :
                (
                    !FormatUtil::checkStringEmpty($this->phonework) ?
                        $this->phonework
                        :
                        (
                            !FormatUtil::checkStringEmpty($this->phonehome) ?
                                $this->phonehome
                                :
                                (
                                    !FormatUtil::checkStringEmpty($this->email) ?
                                        $this->email
                                        :
                                        ''
                                )
                        )
                );
    }

    public function toSearchableArray()
    {
        return [
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'company' => $this->company,
            'phonemobile' => $this->phonemobile,
            'phonehome' => $this->phonehome,
            'phonework' => $this->phonework,
            'phonefax' => $this->phonefax,
            'email' => $this->email,
            'address1' => $this->address1,
            'address2' => $this->address2,
            'suburb' => $this->suburb,
            'state' => $this->state,
            'postcode' => $this->postcode,
            'notes' => $this->notes,
            'deal_id' => $this->deal_id,
            'contact_id' => $this->contact_id,
            'contacttype_id' => $this->contacttype_id,
            'phone' => $this->phone,
            'user_id' => $this->user_id
        ];
    }

    protected $table = 'dealcontacts';
}
