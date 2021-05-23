<?php

namespace App\Observers;

use App\Models\LoanSplit;

use App\Http\Controllers\Util\JournalUtil;
use App\Http\Controllers\Util\ContactUtil;

use App\Datas\JournalTemp;

class LoanSplitObserver
{
    /**
     * Handle the loan split "created" event.
     *
     * @param  \App\LoanSplit  $loanSplit
     * @return void
     */
    public function created(LoanSplit $loanSplit)
    {
        if($loanSplit->referrer_id !==null && $loanSplit->referrer_id !== '')
        {
            JournalUtil::saveJournalEntry(
                sprintf(
                    JournalTemp::$ReferrerNew,
                    $loanSplit->referrer->id,
                    $loanSplit->referrer->firstname,
                    $loanSplit->referrer->lastname
                ),
                null,
                $loanSplit->deal_id
            );
        }
    }

    /**
     * Handle the loan split "updated" event.
     *
     * @param  \App\LoanSplit  $loanSplit
     * @return void
     */
    public function updated(LoanSplit $loanSplit)
    {
        if($loanSplit->wasChanged('referrer_id'))
        {
            $referrer = $loanSplit->referrer;
            $_referrer = ContactUtil::getContactById($loanSplit->getOriginal('referrer_id'));
            JournalUtil::saveJournalEntry(
                sprintf(
                    JournalTemp::$ReferrerUpdate,
                    $_referrer->id,
                    $_referrer->firstname,
                    $_referrer->lastname,
                    $referrer->id,
                    $referrer->firstname,
                    $referrer->lastname
                ),
                null,
                $loanSplit->deal_id
            );
        }
    }

    /**
     * Handle the loan split "deleted" event.
     *
     * @param  \App\LoanSplit  $loanSplit
     * @return void
     */
    public function deleted(LoanSplit $loanSplit)
    {
        //
    }

    /**
     * Handle the loan split "restored" event.
     *
     * @param  \App\LoanSplit  $loanSplit
     * @return void
     */
    public function restored(LoanSplit $loanSplit)
    {
        //
    }

    /**
     * Handle the loan split "force deleted" event.
     *
     * @param  \App\LoanSplit  $loanSplit
     * @return void
     */
    public function forceDeleted(LoanSplit $loanSplit)
    {
        //
    }
}
