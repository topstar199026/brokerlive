<?php

namespace App\Datas;

class WhiteBoardData 
{
    public $section_id;
    public $section_name;
    public $total_loan;
    public $total_actual;
    public $rows;
    public $row_count;

    public function __construct($sectionName) {
        $this->rows = array();
        
        $this->section_name = $sectionName;
        
        $this->row_count = 0;
        $this->total_loan = 0;
        $this->total_actual = 0;
    }

    public function addRow($row)
    {
        $this->rows[] = $row;
        
        $this->row_count++;
        
        if ($row->loan_amount != '') {
            $this->total_loan += $row->loan_amount;
        }
        if ($row->actual != '') {
            $this->total_actual += $row->actual;
        }
    }
}
