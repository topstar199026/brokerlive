<?php

namespace App\Datas;

use App\Datas\SelectOptionData;

class SelectOptionGroupData 
{
    public $label;
    
    public $options;
    
    public function __construct($label) {
        $this->options = array();
        $this->label = $label;
    }
    
    public function add_option($description, $value, $selected = false) {
        $this->options[] = new SelectOptionData($description, $value, $selected);
    }
}
