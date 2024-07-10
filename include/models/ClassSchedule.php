<?php

require_once dirname(__FILE__) . '/../DbTable.php';

class ClassSchedule extends DbTable
{
    function __construct()
    {
        $this->table = 'class_schedule';
    }
}
