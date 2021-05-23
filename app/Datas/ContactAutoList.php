<?php

namespace App\Datas;

class ContactAutoList 
{
    public $label;
    public $value;
    public $data;
    public function __construct($label, $value, $data)
    {
        $this->label = $label;
        $this->value = $value;
        $this->data = $data;
    }
}
