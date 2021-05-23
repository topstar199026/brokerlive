<?php

namespace App\Http\Controllers\Util;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Builder;

use DB;

use App\Models\Reminder;

use App\Http\Controllers\Util\UserUtil;
use App\Http\Controllers\Util\FormatUtil;

use App\Datas\JournalTemp;

class CalendarUtil extends Controller
{
    public static function getRemindersByDate2($data)
    {
        $start = data_get($data, 'start');
        $end = data_get($data, 'end');
        $start = date('Y-m-d', strtotime($start));
        $end = date('Y-m-d', strtotime($end));

        $_NOW = date('Y-m-d');

        $subReminder = Reminder::leftJoin('deals', 'deals.id', '=', 'reminders.deal_id')
            ->whereIn('user_id', UserUtil::getBrockerIds())
            ->where(function($query) use ($_NOW) {
                $query->where('duedate', '>=', $_NOW)
                      ->orWhere('completed', '=', 1);
            })
            ->select(
                DB::raw('DATE_FORMAT(reminders.duedate, \'%Y-%m-%d\') AS duedate'),
                'reminders.who_for',
                'reminders.completed',
                'reminders.starttime',
                'reminders.timelength',
                'reminders.id',
                'deals.name'
            );

        //return $subReminder->getQuery();
        return $reminder = DB::table(DB::raw('('.$subReminder->toSql().') as subReminder'))
            ->mergeBindings($subReminder->getQuery())
            ->whereNotNull('subReminder.duedate')
            ->where('subReminder.duedate', '>=', $start)
            ->where('subReminder.duedate', '<=', $end)
            ->orderBy('subReminder.duedate')
            ->get();
            //return $reminder::groupBy('subReminder.duedate');

    }

    public static function getRemindersByDate2_C($data)
    {

        $start = data_get($data, 'start');
        $end = data_get($data, 'end');
        $start = date('Y-m-d', strtotime($start));
        $end = date('Y-m-d', strtotime($end));

        $_NOW = date('Y-m-d');

        $subReminder = Reminder::leftJoin('deals', 'deals.id', '=', 'reminders.deal_id')
            ->whereIn('user_id', UserUtil::getBrockerIds())
            ->where(function($query) use ($_NOW) {
                $query->where('duedate', '>=', $_NOW)
                      ->orWhere('completed', '=', 1);
            })
            ->select(
                DB::raw('DATE_FORMAT(reminders.duedate, \'%Y-%m-%d\') AS duedate'),
                'reminders.who_for',
                'reminders.completed'
            );

        //return $subReminder->getQuery();
        return $reminder = DB::table(DB::raw('('.$subReminder->toSql().') as subReminder'))
            ->mergeBindings($subReminder->getQuery())
            ->whereNotNull('subReminder.duedate')
            ->where('subReminder.duedate', '>=', $start)
            ->where('subReminder.duedate', '<=', $end)
            ->groupBy('subReminder.duedate')
            ->orderBy('subReminder.duedate')
            ->select(
                'subReminder.duedate',
                DB::raw('GROUP_CONCAT(who_for SEPARATOR \'|\') AS who_for'),
                DB::raw('GROUP_CONCAT(CASE WHEN completed IS NULL THEN 0 ELSE completed END SEPARATOR \'\') AS who_for_completed'),
                DB::raw('SUM(completed IS NULL OR completed = 0) AS incompleted'),
                DB::raw('SUM(completed IS NOT NULL AND completed = 1) AS completed')
            )
            ->get();

    }

    public static function _getRemindersByDate2_C($data)
    {
        $start = data_get($data, 'start');
        $end = data_get($data, 'end');
        $start = date('Y-m-d', strtotime($start));
        $end = date('Y-m-d', strtotime($end));

        $_NOW = date('Y-m-d');

        $subReminder = Reminder::leftJoin('deals', 'deals.id', '=', 'reminders.deal_id')
            ->whereIn('user_id', UserUtil::getBrockerIds())
            ->where(function($query) use ($_NOW) {
                $query->where('duedate', '>=', $_NOW)
                      ->orWhere('completed', '=', 1);
            })
            ->select(
                DB::raw('DATE_FORMAT(reminders.duedate, \'%Y-%m-%d\') AS duedate'),
                'reminders.who_for',
                'reminders.completed'
            );

        //return $subReminder->getQuery();
        return $reminder = DB::table(DB::raw('('.$subReminder->toSql().') as subReminder'))
            ->mergeBindings($subReminder->getQuery())
            ->whereNotNull('subReminder.duedate')
            ->where('subReminder.duedate', '>=', $start)
            ->where('subReminder.duedate', '<=', $end)
            ->groupBy('subReminder.duedate')
            ->orderBy('subReminder.duedate')
            ->select(
                'subReminder.duedate',
                DB::raw('GROUP_CONCAT(who_for SEPARATOR \'|\') AS who_for'),
                DB::raw('GROUP_CONCAT(CASE WHEN completed IS NULL THEN 0 ELSE completed END SEPARATOR \'\') AS who_for_completed'),
                DB::raw('SUM(completed IS NULL OR completed = 0) AS incompleted'),
                DB::raw('SUM(completed IS NOT NULL AND completed = 1) AS completed')
            )
            ->get();

    }

    public static function getRemindersByDate_2($data)
    {
        $start = data_get($data, 'start');
        $end = data_get($data, 'end');
        $start = date('Y-m-d', strtotime($start));
        $end = date('Y-m-d', strtotime($end));

        $subReminder = Reminder::leftJoin('deals', 'deals.id', '=', 'reminders.deal_id')
            ->whereIn('user_id', UserUtil::getBrockerIds())
            ->select(
                DB::raw('DATE_FORMAT(CASE
                WHEN reminders.completed IS NOT NULL AND reminders.completed = 1 THEN reminders.updated_at
                ELSE reminders.duedate
                END, \'%Y-%m-%d\') AS duedate'),
                'reminders.who_for',
                'reminders.completed'
            );

        //return $subReminder->getQuery();
        return $reminder = DB::table(DB::raw('('.$subReminder->toSql().') as subReminder'))
            ->mergeBindings($subReminder->getQuery())
            ->whereNotNull('subReminder.duedate')
            ->where('subReminder.duedate', '>=', $start)
            ->where('subReminder.duedate', '<=', $end)
            ->groupBy('subReminder.duedate')
            ->orderBy('subReminder.duedate')
            ->select(
                'subReminder.duedate',
                DB::raw('GROUP_CONCAT(who_for SEPARATOR \'|\') AS who_for'),
                DB::raw('GROUP_CONCAT(CASE WHEN completed IS NULL THEN 0 ELSE completed END SEPARATOR \'\') AS who_for_completed'),
                DB::raw('SUM(completed IS NULL OR completed = 0) AS incompleted'),
                DB::raw('SUM(completed IS NOT NULL AND completed = 1) AS completed')
            )
            ->get();

    }
    public static function getRemindersByDate($data)
    {
        $start = data_get($data, 'start');
        $end = data_get($data, 'end');
        $start = date('Y-m-d', strtotime($start));
        $end = date('Y-m-d', strtotime($end));

        // $subReminder = Reminder::leftJoin('dealsa', 'deals.id', '=', 'reminders.deal_id')
        //     ->whereIn('user_id', UserUtil::getBrockerIds())
        //     ->select(
        //         DB::raw('DATE_FORMAT(CASE
        //         WHEN r.completed IS NOT NULL AND r.completed = 1 THEN r.stamp_updated
        //         ELSE r.duedate
        //         END, \'%Y-%m-%d\') AS duedate'),
        //         'reminders.who_for',
        //         'reminders.completed',
        //     );

        // //return $reminder->toSql();
        // $reminder = DB::table(DB::raw('({$subReminder->toSql()}) as subReminder'))
        //     ->mergeBindings($subReminder)
        //     // ->whereNotNull('subReminder.duedate')
        //     // ->where('subReminder.duedate', '>=', $start)
        //     // ->where('subReminder.duedate', '<=', $end)
        //     // ->groupBy('subReminder.duedate')
        //     // ->orderBy('subReminder.duedate')
        //     // ->select(
        //     //     'subReminder.duedate',
        //     //     DB::raw('GROUP_CONCAT(who_for SEPARATOR \'|\') AS who_for'),
        //     //     DB::raw('GROUP_CONCAT(CASE WHEN completed IS NULL THEN 0 ELSE completed END SEPARATOR \'\') AS who_for_completed'),
        //     //     DB::raw('SUM(completed IS NULL OR completed = 0) AS incompleted'),
        //     //     DB::raw('SUM(completed IS NOT NULL AND completed = 1) AS completed')
        //     ->get();



        $query = "
            SELECT
                duedate,
                /* Instead of multiple group by select statements, we concat the values of who_for */
                /* then we post-process and count them */
                GROUP_CONCAT(who_for SEPARATOR '|') AS who_for,
                GROUP_CONCAT(CASE WHEN completed IS NULL THEN 0 ELSE completed END SEPARATOR '') AS who_for_completed,
                SUM(completed IS NULL OR completed = 0) AS incompleted,
                SUM(completed IS NOT NULL AND completed = 1) AS completed
            FROM
                (SELECT
                    DATE_FORMAT(CASE
                        WHEN r.completed IS NOT NULL AND r.completed = 1 THEN r.created_at
                        ELSE r.duedate
                    END, '%Y-%m-%d') AS duedate,
                    r.who_for, r.completed
                FROM reminders r LEFT JOIN deals ON deals.id = r.deal_id
                WHERE
                    deals.user_id IN (" . implode(',', UserUtil::getBrockerIds()) . ")
                ) AS reminders_per_date
            WHERE
                duedate IS NOT NULL
                AND duedate >= '$start' AND duedate <= '$end'
            GROUP BY duedate
            ORDER BY duedate
        ";

        return DB::select($query);

    }

    public static function transformRemindersData($data)
    {
        // who_for is a string concaternation of all who_for, for example broker|pa|broker,pa|pa...
        // who_for_completed is a flag concaternation, relatively map to who_for, for example 1110...
        // 1 on 1 map
        //    broker - 1
        //    pa     - 1
        //    broker - 1   this is only 1 reminder but for both 'broker' and 'pa'  <<< THIS NEED MORE CLARIFICATIONS!
        //    pa     - 1   delimit by ',' so we need to increase both broker and pa
        //    pa     - 0
        //  => 'broker' has 2 completed
        //     'pa'     has 2 completed

        $transform = array();
        $start = 0;
        if (isset($data[0]->overdue_count)) {
            $start = 1;
            array_push($transform, $data[0]);
        }

        for ($i = $start; $i < count($data); $i++) {
            if (isset($data[$i]->who_for)) {
                $whoFor = explode('|', $data[$i]->who_for);
                $whoForCompleted = $data[$i]->who_for_completed;
                $curDate = array('duedate' => $data[$i]->duedate);
                $whoCount = array();
                for ($whoIndex = 0; $whoIndex < count($whoFor); $whoIndex++) {
                    $whoArray = explode(',', $whoFor[$whoIndex]);

                    // we increase count for all who_for delimited by ','
                    // which is from 1 reminder
                    foreach ($whoArray as $who) {
                        if (!isset($whoCount[$who])) {
                            $whoCount[$who] = array();
                        }

                        if (!isset($whoCount[$who]['completed'])) {
                            $whoCount[$who]['completed'] = 0;
                        }

                        if (!isset($whoCount[$who]['incompleted'])) {
                            $whoCount[$who]['incompleted'] = 0;
                        }

                        if ($whoForCompleted[$whoIndex] == '1') {
                            $whoCount[$who]['completed']++;
                        } else {
                            $whoCount[$who]['incompleted']++;
                        }
                    }
                }
                $curDate['who'] = $whoCount;
                array_push($transform, $curDate);
            }
        }
        return $transform;
    }

    public static function isTodayIncluded($data)
    {
        $start = data_get($data, 'start');
        $end = data_get($data, 'end');
        $month = data_get($data, 'month', null);

        $today = date('Y-m-d');
        if (!empty($start) && !empty($end)) {
            return $start <= $today && $today <= $end;
        } else {
            list($month, $year) = self::extractMonthYear($month);
            return $month === date('m') && $year === date('Y');
        }
    }

    private static function extractMonthYear($month)
    {
        if (strlen($month) < 5) {
            // wrong length, return current month
            return explode('-', date('m-Y'));
        }
        if (strlen($month) < 6) {
            // only got MYYYY
            return array(substr($month, 0, 1), substr($month, 1));
        }

        if ($pos = strpos($month, '-')) {
            // got MM-YYYY
            return array(substr($month, 0, $pos), substr($month, $pos + 1));
        }

        // got MMYYYY
        return array(substr($month, 0, 2), substr($month, 2));
    }

    public static function getOverdueCount($data)
    {
        return $subReminder = Reminder::leftJoin('deals', 'deals.id', '=', 'reminders.deal_id')
            ->whereIn('user_id', UserUtil::getBrockerIds())
            ->whereNotNull('duedate')
            ->where('duedate', '<', DB::raw('CURDATE()'))
            ->where(function($query){
                $query->whereNull('completed')
                    ->orWhere('completed', '!=', '1');
            })
            ->select(DB::raw('COUNT(duedate) AS overdue_count'), DB::raw('CURRENT_DATE() AS duedate'))
            ->get();
    }
}
