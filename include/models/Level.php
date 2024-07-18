<?php

require_once dirname(__FILE__) . '/../DbTable.php';

class Level extends DbTable
{
    function __construct()
    {
        $this->table = 'level';
    }
}
