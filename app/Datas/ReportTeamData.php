<?php

namespace App\Datas;

class ReportTeamData {
    
    public $leads = 0;
    public $calls = 0;
    public $appts = 0;
    public $submissions = 0;
    public $submissions_number = 0;
    public $preapp = 0;
    public $pending = 0;
    public $fullapp = 0;
    public $settled = 0;
    
    public function add_row($row) {
        $this->leads += $row['Leads'];
        $this->calls += $row['Calls'];
        $this->appts += $row['Appts'];
        $this->submissions += $row['Submissions'];
        $this->submissions_number += $row['SubmissionsNumber'];
        $this->preapp += $row['Preapp'];
        $this->pending += $row['Pending'];
        $this->fullapp += $row['Fullapp'];
        $this->settled += $row['Settled'];
    }
}