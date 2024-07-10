<?php

require_once dirname(__FILE__) . '/../DbTable.php';

class College extends DbTable
{
    function __construct()
    {
        $this->table = 'college';
    }
}
