<?php

require_once dirname(__FILE__) . '/../DbTable.php';

class Program extends DbTable
{
    function __construct()
    {
        $this->table = 'program';
    }

    function getProgram($department_id, $name)
    {
        $this->connect();

        $sql = "SELECT * FROM $this->table WHERE department_id = $department_id and name = '$name'";
        $query = $this->conn->query($sql);
        if (!$query) {
            $row = null;
        } else {
            $row = $query->fetch_assoc();
        }

        $this->conn->close();
        return $row;
    }
}
