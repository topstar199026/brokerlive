<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Util\ReportUtil;
use Illuminate\Http\Request;

define('APPPATH', realpath(dirname(__FILE__).'/../../../').DIRECTORY_SEPARATOR);

class ReportController extends Controller
{
    public function index(Request $request)
    {
        return view('pages.report.index',[
            'title' => "Report",
            ]
        ); 
    }

    public function NestedReferrerApi(Request $request){
        $userId =  Auth::id();
        $status = $request->query("status");
        $reportName='NestedReferrer';

        if ($status === "true") {
            $report = 'Helper_Report_' . $reportName;

            $getStatusName = "RefreshNestedReferrerData_user_{$userId}";
            return ReportUtil::getStatus($getStatusName);
        }
        
        $cache = $request->query("cache");
        $cache = $cache === 'false' ? false : true;

        $data = $this->generateReportData($reportName,$userId,array(
            'cache' => $cache
        ));

        if ($data === null) {
            return array(
                "message" => "No report has been generated. Please specify ?cache=false specifically to generate and cache the report."
            );
        } else {
            return $data;
        }
    }

    private function generateReportData($reportName, $brokerId, $options = null)
    {
        $cacheDir = APPPATH.'storage\logs\report_cache';
        $cacheFile = "$brokerId-$reportName-Report.json";
        if (!file_exists($cacheDir)) {
            mkdir($cacheDir);
        }
        $cache = true;
        if (isset($options['cache'])) {
            $cache = $options['cache'];
        }
        if ($cache) {
            if (file_exists("$cacheDir/$cacheFile")) {
                return json_decode(file_get_contents("$cacheDir/$cacheFile"));
            }
        }
        if (file_exists("$cacheDir/$cacheFile")) {
            unlink("$cacheDir/$cacheFile");
        }

        $data = ReportUtil::prepareData($brokerId, $options);
        if (isset($data['status']) && $data['status'] == 'finished') {
            file_put_contents("$cacheDir/$cacheFile", json_encode($data));
        }
        return $data;
    }
}
