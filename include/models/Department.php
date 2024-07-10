<?php

require_once dirname(__FILE__) . '/../DbTable.php';

class Department extends DbTable
{
    function __construct()
    {
        $this->table = 'department';
    }
}
