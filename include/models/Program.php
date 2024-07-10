<?php

require_once dirname(__FILE__) . '/../DbTable.php';

class Program extends DbTable
{
    function __construct()
    {
        $this->table = 'program';
    }

    function getProgram($name)
    {
        $this->connect();

        $sql = "SELECT * FROM $this->table WHERE name = '$name'";
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
