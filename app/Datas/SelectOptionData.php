<?php

namespace App\Datas;

class SelectOptionData 
{
    public $value;
    public $description;
    public $selected;
    
    public function __construct($description, $value, $selected = false) {
        $this->description = $description;
        $this->value = $value;
        $this->selected = $selected;
    }
}
