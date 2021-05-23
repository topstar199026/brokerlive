<?php

namespace App\Observers;

use App\Models\Reminder;

class ReminderObserver
{
    /**
     * Handle the reminder "created" event.
     *
     * @param  \App\Reminder  $reminder
     * @return void
     */
    public function created(Reminder $reminder)
    {
        //
    }

    /**
     * Handle the reminder "updated" event.
     *
     * @param  \App\Reminder  $reminder
     * @return void
     */
    public function updated(Reminder $reminder)
    {
        //
    }

    /**
     * Handle the reminder "deleted" event.
     *
     * @param  \App\Reminder  $reminder
     * @return void
     */
    public function deleted(Reminder $reminder)
    {
        //
    }

    /**
     * Handle the reminder "restored" event.
     *
     * @param  \App\Reminder  $reminder
     * @return void
     */
    public function restored(Reminder $reminder)
    {
        //
    }

    /**
     * Handle the reminder "force deleted" event.
     *
     * @param  \App\Reminder  $reminder
     * @return void
     */
    public function forceDeleted(Reminder $reminder)
    {
        //
    }
}
