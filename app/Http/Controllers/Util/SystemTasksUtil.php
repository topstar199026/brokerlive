<?php

namespace App\Http\Controllers\Util;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use App\Models\TaskStatus;
define('DOCROOT', realpath(dirname(__FILE__).'/../../').DIRECTORY_SEPARATOR);

class SystemTasksUtil extends Controller
{
    public static function getSystemTasks(){
        $data = TaskStatus::select()->orderBy('start_time', 'DESC')->get();
        return (array(
            "data" => $data,
            "error" => null,
            "status" => "success"
        ));
    }

    public static function runTask($task, $params, $suffix = "", $debug = false){
        $config = "";
        $environment="";
        $phpPath = isset($config['task_php_executable']) ? $config['task_php_executable'] : PHP_BINARY;
        $envPhp = trim(getenv("PHPEXEC"));
        if (isset($envPhp) && !empty($envPhp)) {
            $phpPath = $envPhp;
        }
        $phpIniPath = isset($config['task_php_ini']) ? $config['task_php_ini'] : php_ini_loaded_file();
        $paramString = "";
        if (is_string($params)) {
            $paramString = $params;
            if (strpos($params, ' --env=') === false) {
                $paramString .= ' --env=' . $environment;
            }
        } elseif (is_array($params)) {
            if (!isset($params['env'])) {
                $params['env'] = $environment;
            }
            foreach ($params as $key => $value) {
                $paramString .= " --$key=$value";
            }
        }
        $docRoot = DOCROOT;
        $cmd = "$phpPath -c $phpIniPath $docRoot" . "index.php --task=$task $paramString $suffix";
        $output = "";
        switch (strtolower(PHP_OS)) {
            case "winnt":
                if (!isset($phpPath) || empty($phpPath)) {
                    throw new HTTP_Exception_500("Windows Server is detected. Please set PHPEXEC System Environment Variable to PHP Executable file, and set: variables_order = \"EGPCS\" in php.ini file");
                }
                $cmd = "start /B $cmd";
                pclose(popen($cmd, "r"));
                $output = "No shell output can be obtained on Windows...";
                break;
            default:
                $cmd = "$cmd | at now";
                exec(sprintf("%s > dev/null 2>&1 & echo $!", $cmd), $pidArr);
                $output = "Process started, PID: " . $pidArr[0];
                break;
        }
        $res=array(
            'task' => $task,
            'command' => $cmd,
            'output' => $output
        );
        return $res;
    }
    public static function getStatus($name, $queryStatus = false)
    {
        $status = TaskStatus::select();
        if ($queryStatus) {
            $status = $status->where('status', '=', $queryStatus);
        }
        $status = $status->where('name', '=', $name)->first();
        return $status;
    }

    public static function setStatus($name, $queryStatus = 'running', $queryResult = null)
    {
        $status = TaskStatus::select()->where('name', '=', $name)->find();
        $status->name = $name;
        $status->status = $queryStatus;
        $status->result = $queryResult;
        $status->save();
        return $status;
    }
}
