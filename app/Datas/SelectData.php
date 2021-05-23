<?php

namespace App\Datas;

use App\Datas\SelectOptionData;

class SelectData 
{
    public $id;
    public $name;
    public $multiple;
    public $class;
    
    public $options;

    public function __construct() {
        $this->options = array();
    }
    
    public function add_optiongroup($optiongroup)
    {
        $this->options[] = $optiongroup;
    }
    
    public function add_option($description, $value)
    {
        $this->options[] = new SelectOptionData($description, $value);
    }
}
