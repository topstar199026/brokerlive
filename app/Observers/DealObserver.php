<?php

namespace App\Observers;

use App\Models\Deal;

use App\Http\Controllers\Util\DealUtil;
use App\Http\Controllers\Util\JournalUtil;

use App\Datas\JournalTemp;

class DealObserver
{
    /**
     * Handle the deal "created" event.
     *
     * @param  \App\Deal  $deal
     * @return void
     */
    public function created(Deal $deal)
    {
        $parentId = $deal->parent_id;
        if($parentId !== null && $parentId !== '') {
            JournalUtil::saveJournalEntry(
                sprintf(
                    JournalTemp::$DealCloned,
                    DealUtil::getDealByPId($deal)->name
                ),
                null,
                $deal->id
            );
        }
        else
        {
            JournalUtil::saveJournalEntry(
                JournalTemp::$DealCreated,
                null,
                $deal->id
            );
        }
    }

    /**
     * Handle the deal "updated" event.
     *
     * @param  \App\Deal  $deal
     * @return void
     */
    public function updated(Deal $deal)
    {
        if($deal->wasChanged('status'))
        {
            JournalUtil::saveJournalEntry(
                sprintf(
                    JournalTemp::$DealStatusUpdate,
                    DealUtil::getDealStatusById($deal->getOriginal('status'))->description,
                    $deal->dealstatus->description
                ),
                null,
                $deal->id
            );
        }
    }

    /**
     * Handle the deal "deleted" event.
     *
     * @param  \App\Deal  $deal
     * @return void
     */
    public function deleted(Deal $deal)
    {
        //
    }

    /**
     * Handle the deal "restored" event.
     *
     * @param  \App\Deal  $deal
     * @return void
     */
    public function restored(Deal $deal)
    {
        //
    }

    /**
     * Handle the deal "force deleted" event.
     *
     * @param  \App\Deal  $deal
     * @return void
     */
    public function forceDeleted(Deal $deal)
    {
        //
    }
}
