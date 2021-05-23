<?php

namespace App\Datas;

class JournalTemp
{
    public static $DealCreated = 'Lead created';
    public static $DealCloned = 'Lead cloned: %1$s';
    public static $DealStatusUpdate = 'Status updated : %1$s > %2$s';

    public static $ReminderCompleted = 'Task actioned by: %1$s %2$s, for: %3$s, types: %4$s<br><br>Reminder actioned:<br>%5$s<br><br>Comments:<br>%6$s';
    public static $ReminderRepeated = 'Task repeated by: %1$s %2$s. Task due / for: %3$s / %4$s<br><br>Reminder actioned:<br>%5$s<br><br>Comments:<br>%6$s';
    public static $ReminderDeleted = 'Task deleted by: %1$s %2$s<br><br>Reminder:<br>%3$s';
    public static $ReminderCreated = 'Task created by: %1$s %2$s. Task due / for: %3$s / %4$s';
    public static $ReminderChanged = 'Task due date changed: Task due %1$s Reminder updated by: %2$s';

    public static $ReferrerNew = 'Referrer set to "%2$s %3$s - %1$s"';
    public static $ReferrerUpdate = 'Referrer "%2$s %3$s - %1$s" changed to "%5$s %6$s - %4$s"';
}
