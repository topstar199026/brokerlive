<?php

namespace App\Datas;

class ModelTreeDeal
{
    public $deal;
    public $children;

    public function __construct($deal)
    {
        $this->deal = $deal;
        $this->children = array();
    }

    public function addChild($deal)
    {
        $this->children[] = new ModelTreeDeal($deal);
    }

    public function addBranch($branch)
    {
        $this->children[] = $branch;
    }
}

