<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Http\Controllers\Util\FormatUtil;

class LoanSplit extends Model
{
    public function _financeduedate()
    {
        return $this->financeduedate ? date('d M Y', strtotime($this->financeduedate)) : '';
    }

    public function _settlementdate()
    {
        return $this->settlementdate ? date('d M Y', strtotime($this->settlementdate)) : '';
    }

    public function _initial_appointment()
    {
        return $this->initial_appointment ? date('d M Y', strtotime($this->initial_appointment)) : '';
    }

    public function _submitted()
    {
        return $this->submitted ? date('d M Y', strtotime($this->submitted)) : '';
    }

    public function _aip()
    {
        return $this->aip ? date('d M Y', strtotime($this->aip)) : '';
    }

    public function _conditional()
    {
        return $this->conditional ? date('d M Y', strtotime($this->conditional)) : '';
    }

    public function _approved()
    {
        return $this->approved ? date('d M Y', strtotime($this->approved)) : '';
    }

    public function _settled()
    {
        return $this->settled ? date('d M Y', strtotime($this->settled)) : '';
    }

    public function _discharged()
    {
        return $this->discharged ? date('d M Y', strtotime($this->discharged)) : '';
    }

    public function _commission_paid_trail()
    {
        return $this->commission_paid_trail ? date('d M Y', strtotime($this->commission_paid_trail)) : '';
    }

    public function _commission_paid_value()
    {
        return $this->commission_paid_value ? date('d M Y', strtotime($this->commission_paid_value)) : '';
    }

    public function _notproceeding()
    {
        return $this->notproceeding ? date('d M Y', strtotime($this->notproceeding)) : '';
    }
    
    public function _subloan()
    {
        return FormatUtil::numberFormat($this->subloan, 0);
    }

    public function _submittedtrail()
    {
        return FormatUtil::numberFormat($this->submittedtrail, 0);
    }

    public function _submittedvalue()
    {
        return FormatUtil::numberFormat($this->submittedvalue, 0);
    }

    public function _aiptrail()
    {
        return FormatUtil::numberFormat($this->aiptrail, 0);
    }

    public function _aipvalue()
    {
        return FormatUtil::numberFormat($this->aipvalue, 0);
    }

    public function _conditionaltrail()
    {
        return FormatUtil::numberFormat($this->conditionaltrail, 0);
    }

    public function _conditionalvalue()
    {
        return FormatUtil::numberFormat($this->conditionalvalue, 0);
    }

    public function _approvedtrail()
    {
        return FormatUtil::numberFormat($this->approvedtrail, 0);
    }

    public function _approvedvalue()
    {
        return FormatUtil::numberFormat($this->approvedvalue, 0);
    }

    public function _settledtrail()
    {
        return FormatUtil::numberFormat($this->settledtrail, 0);
    }

    public function _settledvalue()
    {
        return FormatUtil::numberFormat($this->settledvalue, 0);
    }

    public function documentstatus()
    {
        return $this->belongsTo('App\Models\DocumentStatus','documentstatus_id');
    }

    public function deal()
    {
        return $this->belongsTo('App\Models\Deal','deal_id');
    }

    public function referrer()
    {
        return $this->belongsTo('App\Models\Contact','referrer_id');
    }

    public function applicants()
    {
        return $this->hasManyThrough(
            'App\Models\Contact',
            'App\Models\LoanApplicant',
            'loansplit_id',
            'id',
            'id',
            'applicant_id'
        );
    }

    public function getTags() {
        return explode(',', $this->tags);
    }

    public function hasTag($tag)
    {
        return in_array($tag, $this->getTags()) ? true : false;
    }    

    protected $table = 'loansplits';
}
