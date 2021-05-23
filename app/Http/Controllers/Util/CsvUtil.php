<?php

namespace App\Http\Controllers\Util;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;
use DB;
use Response;

use App\Models\Deal;
use App\Models\Contact;
use App\Models\Dealstatus;
use App\Models\DealNotify;
use App\Models\DealContact;
use App\Models\ContactType;
use App\Models\ContentTag;
use App\Models\Role;
use App\Models\FileManagement;
use App\Models\PersonTitle;
use App\Models\LoanSplit;
use App\Models\LoanApplicant;
use App\Models\DocumentStatus;
use App\Models\Lender;

use App\Http\Controllers\Util\LoanSplitUtil;
use App\Http\Controllers\Util\WhiteboardUtil;
use App\Http\Controllers\Util\TeamUtil;

use App\Datas\ModelTreeDeal;
use App\Datas\ContactAutoList;




class CsvUtil extends Controller
{
    private static function buildPipelineCsvSection($section, $rows)
    {
        $csv_array = array();

        $loan_total = 0;
        $actual_total = 0;

        foreach ($rows as $row)
        {
            $csv_array[] = array(
                $row->broker,
                $row->borrower,
                $row->settlement_date,
                $row->finance_due,
                $row->referrer,
                $row->lender,
                $row->loan_amount,
                $row->actual,
                $row->doc_status,
                $row->submitted_date,
                $row->aip,
                $row->conditional,
                $row->full_approval
            );

            $loan_total += $row->loan_amount;
            $actual_total += $row->actual;
        }

        $title_row = array($section, '', '', '', '', '', $loan_total, $actual_total, '', '', '', '', '');
        array_unshift($csv_array, $title_row);
        return $csv_array;
    }

    private static function groupMonths($section, $rows)
    {
        $grouped_rows = array();
        $group_rows = array();

        $month = '';
        $loan_total = 0;
        $actual_total = 0;

        $loan_subtotal = 0;
        $actual_subtotal = 0;

        foreach($rows as $row) {
            if ($month != $row->month)
            {
                if ($month != '') {
                    $grouped_rows[] = array($month, '', '', '', '', '', $loan_subtotal, $actual_subtotal, '', '', '', '', '');
                    $grouped_rows = array_merge($grouped_rows, $group_rows);
                }
                $month = $row->month;
                $loan_subtotal = 0;
                $actual_subtotal = 0;
                $group_rows = array();
            }

            $loan_total += $row->loan_amount;
            $loan_subtotal += $row->loan_amount;

            $actual_total += $row->actual;
            $actual_subtotal += $row->actual;

            $group_rows[] = array(
                $row->broker,
                $row->borrower,
                $row->settlement_date,
                $row->finance_due,
                $row->referrer,
                $row->lender,
                $row->loan_amount,
                $row->actual,
                $row->doc_status,
                $row->submitted_date,
                $row->aip,
                $row->conditional,
                $row->full_approval
            );
        }

        $grouped_rows[] = array($month, '', '', '', '', '', $loan_subtotal, $actual_subtotal, '', '', '', '', '');
        $grouped_rows = array_merge($grouped_rows, $group_rows);
        $grouped_rows[] = array('', '', '', '', '', '', $loan_total, $actual_total, '', '', '', '', '');

        return $grouped_rows;
    }

    public static function generatePipelineCsv($section, $filter)
    {
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=Whiteboard-ActivePipeline-".($section? $section.'-':'').date("Y-m-d H:i:s").".csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

        $columns = array('Bkr', 'Borrower', 'Settlement', 'Finance Due', 'Referrer', 'Lender', 'Loan Amount', 'Actual', '', 'Submitted', 'AIP', 'Pending', 'Full App');

        $rows = [];
        $_rows = [];
        $_section = '';
        switch ($section)
        {
            case 'settled':
                $_section = 'Settled';
                $_rows = LoanSplitUtil::getSettledWhiteboard($filter);
                $rows = self::buildPipelineCsvSection($_section, $_rows->rows);
                break;
            case 'approved':
                $_section = 'Unconditional Approvals';
                $_rows = LoanSplitUtil::getApprovedWhiteboard($filter);
                $rows = self::buildPipelineCsvSection($_section, $_rows->rows);
                break;
            case 'pending':
                $_section = 'Pending Approval';
                $_rows = LoanSplitUtil::getPendingWhiteboard($filter);
                $rows = self::buildPipelineCsvSection($_section, $_rows->rows);
                break;
            case 'aip':
                $_section = 'Approved in Principle';
                $_rows = LoanSplitUtil::getAipWhiteboard($filter);
                $rows = self::buildPipelineCsvSection($_section, $_rows->rows);
                break;
            case 'submitted':
                $_section = 'Submitted';
                $_rows = LoanSplitUtil::getSubmittedWhiteboard($filter);
                $rows = self::buildPipelineCsvSection($_section, $_rows->rows);
                break;
            case 'committed':
                $_section = 'Committed Clients';
                $_rows = LoanSplitUtil::getCommittedWhiteboard($filter);
                $rows = self::buildPipelineCsvSection($_section, $_rows->rows);
                break;
            case 'hot':
                $_section = 'Hot Clients';
                $_rows = LoanSplitUtil::getHotWhiteboard($filter);
                $rows = self::buildPipelineCsvSection($_section, $_rows->rows);
                break;
            default :
                $_section = 'Unconditional Approvals';
                $_rows = LoanSplitUtil::getApprovedWhiteboard($filter);
                $rows = array_merge($rows, self::buildPipelineCsvSection($_section, $_rows->rows));
                $_section = 'Pending Approval';
                $_rows = LoanSplitUtil::getPendingWhiteboard($filter);
                $rows = array_merge($rows, self::buildPipelineCsvSection($_section, $_rows->rows));
                $_section = 'Approved in Principle';
                $_rows = LoanSplitUtil::getAipWhiteboard($filter);
                $rows = array_merge($rows, self::buildPipelineCsvSection($_section, $_rows->rows));
                $_section = 'Submitted';
                $_rows = LoanSplitUtil::getSubmittedWhiteboard($filter);
                $rows = array_merge($rows, self::buildPipelineCsvSection($_section, $_rows->rows));
                $_section = 'Committed Clients';
                $_rows = LoanSplitUtil::getCommittedWhiteboard($filter);
                $rows = array_merge($rows, self::buildPipelineCsvSection($_section, $_rows->rows));
                $_section = 'Hot Clients';
                $_rows = LoanSplitUtil::getHotWhiteboard($filter);
                $rows = array_merge($rows, self::buildPipelineCsvSection($_section, $_rows->rows));
                $_section = 'Settled';
                $_rows = LoanSplitUtil::getSettledWhiteboard($filter);
                $rows = array_merge($rows, self::buildPipelineCsvSection($_section, $_rows->rows));
        }

        $callback = function() use ($rows, $columns)
        {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach($rows as $row) {
                fputcsv($file,
                    array(
                        $row[0],
                        $row[1],
                        $row[2],
                        $row[3],
                        $row[4],
                        $row[5],
                        $row[6],
                        $row[7],
                        $row[8],
                        $row[9],
                        $row[10],
                        $row[11],
                        $row[12]
                    )
                );
            }
            fclose($file);
        };
        return Response::stream($callback, 200, $headers);
    }

    public static function generateCombinedCsv($section, $filter)
    {
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=Whiteboard-DetailedMonthlyFigures-".($section? $section.'-':'').date("Y-m-d H:i:s").".csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

        $columns = array('Bkr', 'Borrower', 'Settlement', 'Finance Due', 'Referrer', 'Lender', 'Loan Amount', 'Actual', '', 'Submitted', 'AIP', 'Pending', 'Full App');

        $rows = [];
        $_rows = [];
        $__rows = [];
        $_section = '';

        switch ($section)
        {
            case 'settled':
                $_section = 'Settled';
                $_rows = LoanSplitUtil::getSettledWhiteboard($filter, true);
                $__rows = array_merge([array(
                    $_section,
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    ''
                )], self::groupMonths($_section, $_rows->rows));
                $rows = array_merge($rows, $__rows);
                break;
            case 'approved':
                $_section = 'Unconditional Approvals';
                $_rows = LoanSplitUtil::getApprovedWhiteboard($filter, true);
                $__rows = array_merge([array(
                    $_section,
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    ''
                )], self::groupMonths($_section, $_rows->rows));
                $rows = array_merge($rows, $__rows);
                break;
            case 'pending':
                $_section = 'Pending Approval';
                $_rows = LoanSplitUtil::getPendingWhiteboard($filter, true);
                $__rows = array_merge([array(
                    $_section,
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    ''
                )], self::groupMonths($_section, $_rows->rows));
                $rows = array_merge($rows, $__rows);
                break;
            case 'aip':
                $_section = 'Approved in Principle';
                $_rows = LoanSplitUtil::getAipWhiteboard($filter, true);
                $__rows = array_merge([array(
                    $_section,
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    ''
                )], self::groupMonths($_section, $_rows->rows));
                $rows = array_merge($rows, $__rows);
                break;
            case 'submitted':
                $_section = 'Submitted';
                $_rows = LoanSplitUtil::getSubmittedWhiteboard($filter, true);
                $__rows = array_merge([array(
                    $_section,
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    ''
                )], self::groupMonths($_section, $_rows->rows));
                $rows = array_merge($rows, $__rows);
                break;
            default :
                $_section = 'Unconditional Approvals';
                $_rows = LoanSplitUtil::getApprovedWhiteboard($filter, true);
                $__rows = array_merge([array(
                    $_section,
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    ''
                )], self::groupMonths($_section, $_rows->rows));
                $rows = array_merge($rows, $__rows);
                $_section = 'Pending Approval';
                $_rows = LoanSplitUtil::getPendingWhiteboard($filter, true);
                $__rows = array_merge([array(
                    $_section,
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    ''
                )], self::groupMonths($_section, $_rows->rows));
                $rows = array_merge($rows, $__rows);
                $_section = 'Approved in Principle';
                $_rows = LoanSplitUtil::getAipWhiteboard($filter, true);
                $__rows = array_merge([array(
                    $_section,
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    ''
                )], self::groupMonths($_section, $_rows->rows));
                $rows = array_merge($rows, $__rows);
                $_section = 'Submitted';
                $_rows = LoanSplitUtil::getSubmittedWhiteboard($filter, true);
                $__rows = array_merge([array(
                    $_section,
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    ''
                )], self::groupMonths($_section, $_rows->rows));
                $rows = array_merge($rows, $__rows);
                $_section = 'Settled';
                $_rows = LoanSplitUtil::getSettledWhiteboard($filter, true);
                $__rows = array_merge([array(
                    $_section,
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    ''
                )], self::groupMonths($_section, $_rows->rows));
                $rows = array_merge($rows, $__rows);
                break;
        }

        $callback = function() use ($rows, $_section, $columns)
        {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach($rows as $row) {
                fputcsv($file,
                    array(
                        $row[0],
                        $row[1],
                        $row[2],
                        $row[3],
                        $row[4],
                        $row[5],
                        $row[6],
                        $row[7],
                        $row[8],
                        $row[9],
                        $row[10],
                        $row[11],
                        $row[12]
                    )
                );
            }
            fclose($file);
        };
        return Response::stream($callback, 200, $headers);
    }

    public static function generateBasicCsv($filter)
    {
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=Whiteboard-MonthlyFigures-".date("Y-m-d H:i:s").".csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

        $columns = array('Year', 'Months', 'Leads', 'Calls', 'Appts', 'Submissions', 'Pre App', 'Pending', 'Full App', 'Settled');

        $rows = WhiteboardUtil::getWhiteboardRow($filter['fromDate'], $filter['toDate']);

        $callback = function() use ($rows, $columns)
        {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach($rows as $row) {
                fputcsv($file,
                    array(
                        $row[0],
                        $row[1],
                        $row[2],
                        $row[3],
                        $row[4],
                        $row[5],
                        $row[6],
                        $row[7],
                        $row[8],
                        $row[9]
                    )
                );
            }
            fclose($file);
        };
        return Response::stream($callback, 200, $headers);
    }

    public static function generateBusinessCsv($filter)
    {
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=Whiteboard-BusinessMetrics-".date("Y-m-d H:i:s").".csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

        $columns = array('Borrower', 'Initial Appointment', 'Submitted', 'Approved', 'Settled', 'Discharged Date', 'Upfront Paid', 'Trail Paid', 'Appointment to Submission', 'Submission to Approval', 'Approval to Settlement', 'Settled to Upfront', 'Loan Life (from settlement to discharge date)');

        $rows = LoanSplitUtil::getBusinessWhiteboard($filter, true);

        $callback = function() use ($rows, $columns)
        {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach($rows as $row) {
                fputcsv($file,
                    array(
                        $row['borrower'],
                        $row['appts'],
                        $row['submitted'],
                        $row['approved'],
                        $row['settled'],
                        $row['discharged'],
                        $row['upfront'],
                        $row['trail'],
                        $row['appts_submitted'],
                        $row['submitted_approved'],
                        $row['approved_settled'],
                        $row['settled_upfront'],
                        $row['loan_life']
                    )
                );
            }
            fclose($file);
        };
        return Response::stream($callback, 200, $headers);
    }

    public static function generateMarketingCsv($filter)
    {
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=Whiteboard-Marketing-".date("Y-m-d H:i:s").".csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

        $columns = array('Borrower', 'Settlement Date', 'Lender', 'Loan Amount', 'LVR', 'Pipeline Statu', 'Type', 'Email Address', 'Mobile Number', 'Postal Address');

        $rows = DealUtil::getMarketingWhiteboard($filter);

        $callback = function() use ($rows, $columns)
        {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach($rows as $row) {
                fputcsv($file,
                    array(
                        $row['borrower'],
                        $row['settled'],
                        $row['lender'],
                        $row['amount'],
                        $row['lvr'],
                        $row['status'],
                        $row['type'],
                        $row['email'],
                        $row['phone'],
                        $row['postal']
                    )
                );
            }
            fclose($file);
        };
        return Response::stream($callback, 200, $headers);
    }

    public static function generateTeamIndexCsv($filter)
    {
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=Team-Team-".date("Y-m-d H:i:s").".csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

        $columns = array('Broker', 'Leads', 'Calls', 'Appts', 'Submissions', 'Pre App', 'Pending', 'Full App', 'Settled');

        $rows = TeamUtil::queryTeam($filter);

        $callback = function() use ($rows, $columns)
        {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach($rows as $row) {
                fputcsv($file,
                    array(
                        '',
                        $row['leads'],
                        $row['calls'],
                        $row['appts'],
                        $row['submissions'],
                        $row['preapp'],
                        $row['pending'],
                        $row['fullapp'],
                        $row['settled']
                    )
                );
            }
            fclose($file);
        };
        return Response::stream($callback, 200, $headers);
    }

    public static function generateTeamBrokerCsv($filter)
    {
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=Team-Brokers-".date("Y-m-d H:i:s").".csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

        $columns = array('Broker', 'Total Deals');

        $_rows = TeamUtil::queryBroker($filter)->toArray();

        $rows = array_map(
            function($value) {
                return array(
                    'Broker' => $value['firstname'] . ' ' . $value['lastname'],
                    'Total Deals' => $value['deals']
                );
            },
            $_rows
        );

        $callback = function() use ($rows, $columns)
        {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach($rows as $row) {
                fputcsv($file,
                    array(
                        $row['Broker'],
                        $row['Total Deals']
                    )
                );
            }
            fclose($file);
        };
        return Response::stream($callback, 200, $headers);
    }

    public static function generateTeamPipelineCsv($section, $filter)
    {
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=Team-Pipeline-".($section? $section.'-':'').date("Y-m-d H:i:s").".csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

        $columns = array('Bkr', 'Borrower', 'Settlement', 'Finance Due', 'Referrer', 'Lender', 'Loan Amount', 'Actual', '', 'Submitted', 'AIP', 'Pending', 'Full App');

        $rows = [];
        $_rows = [];
        $_section = '';
        switch ($section)
        {
            case 'settled':
                $_section = 'Settled';
                $_rows = TeamUtil::getSettledTeam($filter);
                $rows = self::buildPipelineCsvSection($_section, $_rows->rows);
                break;
            case 'approved':
                $_section = 'Unconditional Approvals';
                $_rows = TeamUtil::getApprovedTeam($filter);
                $rows = self::buildPipelineCsvSection($_section, $_rows->rows);
                break;
            case 'pending':
                $_section = 'Pending Approval';
                $_rows = TeamUtil::getPendingTeam($filter);
                $rows = self::buildPipelineCsvSection($_section, $_rows->rows);
                break;
            case 'aip':
                $_section = 'Approved in Principle';
                $_rows = TeamUtil::getAipTeam($filter);
                $rows = self::buildPipelineCsvSection($_section, $_rows->rows);
                break;
            case 'submitted':
                $_section = 'Submitted';
                $_rows = TeamUtil::getSubmittedTeam($filter);
                $rows = self::buildPipelineCsvSection($_section, $_rows->rows);
                break;
            case 'committed':
                $_section = 'Committed Clients';
                $_rows = TeamUtil::getCommittedTeam($filter);
                $rows = self::buildPipelineCsvSection($_section, $_rows->rows);
                break;
            case 'hot':
                $_section = 'Hot Clients';
                $_rows = TeamUtil::getHotTeam($filter);
                $rows = self::buildPipelineCsvSection($_section, $_rows->rows);
                break;
            default :
                $_section = 'Unconditional Approvals';
                $_rows = TeamUtil::getApprovedTeam($filter);
                $rows = array_merge($rows, self::buildPipelineCsvSection($_section, $_rows->rows));
                $_section = 'Pending Approval';
                $_rows = TeamUtil::getPendingTeam($filter);
                $rows = array_merge($rows, self::buildPipelineCsvSection($_section, $_rows->rows));
                $_section = 'Approved in Principle';
                $_rows = TeamUtil::getAipTeam($filter);
                $rows = array_merge($rows, self::buildPipelineCsvSection($_section, $_rows->rows));
                $_section = 'Submitted';
                $_rows = TeamUtil::getSubmittedTeam($filter);
                $rows = array_merge($rows, self::buildPipelineCsvSection($_section, $_rows->rows));
                $_section = 'Committed Clients';
                $_rows = TeamUtil::getCommittedTeam($filter);
                $rows = array_merge($rows, self::buildPipelineCsvSection($_section, $_rows->rows));
                $_section = 'Hot Clients';
                $_rows = TeamUtil::getHotTeam($filter);
                $rows = array_merge($rows, self::buildPipelineCsvSection($_section, $_rows->rows));
                $_section = 'Settled';
                $_rows = TeamUtil::getSettledTeam($filter);
                $rows = array_merge($rows, self::buildPipelineCsvSection($_section, $_rows->rows));
        }

        $callback = function() use ($rows, $columns)
        {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach($rows as $row) {
                fputcsv($file,
                    array(
                        $row[0],
                        $row[1],
                        $row[2],
                        $row[3],
                        $row[4],
                        $row[5],
                        $row[6],
                        $row[7],
                        $row[8],
                        $row[9],
                        $row[10],
                        $row[11],
                        $row[12]
                    )
                );
            }
            fclose($file);
        };
        return Response::stream($callback, 200, $headers);
    }

    public static function generateTeamCombinedCsv($section, $filter)
    {
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=Team-Stats-".($section? $section.'-':'').date("Y-m-d H:i:s").".csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

        $columns = array('Bkr', 'Borrower', 'Settlement', 'Finance Due', 'Referrer', 'Lender', 'Loan Amount', 'Actual', 'Loan Split Status', 'Submitted', 'AIP', 'Pending', 'Full App');

        $rows = [];
        $_rows = [];
        $_section = '';
        switch ($section)
        {
            case 'settled':
                $_section = 'Settled';
                $_rows = TeamUtil::getSettledTeam($filter, true);
                $__rows = array_merge(
                    [array(
                        $_section,
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        ''
                    )],
                    self::groupMonths($_section, $_rows->rows)
                );
                $rows = array_merge($rows, $__rows);
                break;
            case 'approved':
                $_section = 'Unconditional Approvals';
                $_rows = TeamUtil::getApprovedTeam($filter, true);
                //return $_rows->rows;
                $__rows = array_merge(
                    [array(
                        $_section,
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        ''
                    )],
                    self::groupMonths($_section, $_rows->rows)
                );
                $rows = array_merge($rows, $__rows);
                break;
            case 'pending':
                $_section = 'Pending Approval';
                $_rows = TeamUtil::getPendingTeam($filter, true);
                $__rows = array_merge(
                    [array(
                        $_section,
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        ''
                    )],
                    self::groupMonths($_section, $_rows->rows)
                );
                $rows = array_merge($rows, $__rows);
                break;
            case 'aip':
                $_section = 'Approved in Principle';
                $_rows = TeamUtil::getAipTeam($filter, true);
                $__rows = array_merge(
                    [array(
                        $_section,
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        ''
                    )],
                    self::groupMonths($_section, $_rows->rows)
                );
                $rows = array_merge($rows, $__rows);
                break;
            case 'submitted':
                $_section = 'Submitted';
                $_rows = TeamUtil::getSubmittedTeam($filter, true);
                $__rows = array_merge(
                    [array(
                        $_section,
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        ''
                    )],
                    self::groupMonths($_section, $_rows->rows)
                );
                $rows = array_merge($rows, $__rows);
                break;
            case 'committed':
                $_section = 'Committed Clients';
                $_rows = TeamUtil::getCommittedTeam($filter, true);
                $__rows = array_merge(
                    [array(
                        $_section,
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        ''
                    )],
                    self::groupMonths($_section, $_rows->rows)
                );
                $rows = array_merge($rows, $__rows);
                break;
            case 'hot':
                $_section = 'Hot Clients';
                $_rows = TeamUtil::getHotTeam($filter, true);
                $__rows = array_merge(
                    [array(
                        $_section,
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        ''
                    )],
                    self::groupMonths($_section, $_rows->rows)
                );
                $rows = array_merge($rows, $__rows);
                break;
            default :
                $_section = 'Unconditional Approvals';
                $_rows = TeamUtil::getApprovedTeam($filter, true);
                $__rows = array_merge(
                    [array(
                        $_section,
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        ''
                    )],
                    self::groupMonths($_section, $_rows->rows)
                );
                $rows = array_merge($rows, $__rows);
                $_section = 'Pending Approval';
                $_rows = TeamUtil::getPendingTeam($filter, true);
                $__rows = array_merge(
                    [array(
                        $_section,
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        ''
                    )],
                    self::groupMonths($_section, $_rows->rows)
                );
                $rows = array_merge($rows, $__rows);
                $_section = 'Approved in Principle';
                $_rows = TeamUtil::getAipTeam($filter, true);
                $__rows = array_merge(
                    [array(
                        $_section,
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        ''
                    )],
                    self::groupMonths($_section, $_rows->rows)
                );
                $rows = array_merge($rows, $__rows);
                $_section = 'Submitted';
                $_rows = TeamUtil::getSubmittedTeam($filter, true);
                $__rows = array_merge(
                    [array(
                        $_section,
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        ''
                    )],
                    self::groupMonths($_section, $_rows->rows)
                );
                $rows = array_merge($rows, $__rows);
                $_section = 'Committed Clients';
                $_rows = TeamUtil::getCommittedTeam($filter, true);
                $__rows = array_merge(
                    [array(
                        $_section,
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        ''
                    )],
                    self::groupMonths($_section, $_rows->rows)
                );
                $rows = array_merge($rows, $__rows);
                $_section = 'Hot Clients';
                $_rows = TeamUtil::getHotTeam($filter, true);
                $__rows = array_merge(
                    [array(
                        $_section,
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        ''
                    )],
                    self::groupMonths($_section, $_rows->rows)
                );
                $rows = array_merge($rows, $__rows);
                $_section = 'Settled';
                $_rows = TeamUtil::getSettledTeam($filter, true);
                $__rows = array_merge(
                    [array(
                        $_section,
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        ''
                    )],
                    self::groupMonths($_section, $_rows->rows)
                );
                $rows = array_merge($rows, $__rows);
        }

        $callback = function() use ($rows, $columns)
        {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach($rows as $row) {
                fputcsv($file,
                    array(
                        $row[0],
                        $row[1],
                        $row[2],
                        $row[3],
                        $row[4],
                        $row[5],
                        $row[6],
                        $row[7],
                        $row[8],
                        $row[9],
                        $row[10],
                        $row[11],
                        $row[12]
                    )
                );
            }
            fclose($file);
        };
        return Response::stream($callback, 200, $headers);
    }

    public static function generateTeamBasicCsv($filter)
    {
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=Team-Basic-".date("Y-m-d H:i:s").".csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

        $columns = array('Year', 'Months', 'Leads', 'Calls', 'Appts', 'Submissions', 'Pre App', 'Pending', 'Full App', 'Settled');

        $rows = TeamUtil::queryBasic($filter)->toArray();

        $callback = function() use ($rows, $columns)
        {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach($rows as $row) {
                fputcsv($file,
                    array(
                        $row['year'],
                        $row['month'],
                        $row['leads'],
                        $row['calls'],
                        $row['appts'],
                        $row['submissions'],
                        $row['preapp'],
                        $row['pending'],
                        $row['fullapp'],
                        $row['settled']
                    )
                );
            }
            fclose($file);
        };
        return Response::stream($callback, 200, $headers);
    }

    public static function generateLeadCsv($data)
    {
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=Lead-".date("Y-m-d H:i:s").".csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

        $columns = array('Lead Received Date', 'Lead Name', 'Contact Number', 'Email', 'Reminder / Last journal note', 'Referrer', 'Status');

        $rows = DealUtil::getLeadsForCsv($data)->get()->toArray();
        //return dd($rows->toArray());

        $callback = function() use ($rows, $columns)
        {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach($rows as $row) {
                fputcsv($file,
                    array(
                        $row['received_date'],
                        $row['lead_name'],
                        $row['contact_number'],
                        $row['email'],
                        $row['notes'],
                        $row['referrer']
                    )
                );
            }
            fclose($file);
        };
        return Response::stream($callback, 200, $headers);
    }

    public static function generateJournalCsv($data)
    {
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=Journal-".date("Y-m-d H:i:s").".csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

        $columns = array('Entry Date', 'Actioned By', 'Deal', 'Deal Status', 'Journal Entry');

        $rows = JournalUtil::getJournalsForCSV($data)->toArray();
        //return dd($rows->toArray());

        $callback = function() use ($rows, $columns)
        {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach($rows as $row) {
                fputcsv($file,
                    array(
                        $row['_entrydate'],
                        $row['username'],
                        $row['deal_name'],
                        $row['status_description'],
                        $row['notes']
                    )
                );
            }
            fclose($file);
        };
        return Response::stream($callback, 200, $headers);
    }


}

