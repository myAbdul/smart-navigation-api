<?php

require_once dirname(__FILE__) . '/../DbTable.php';

class User extends DbTable
{
    function __construct()
    {
        $this->table = 'user';
    }

    function getUser($username)
    {
        $this->connect();

        $sql = "SELECT * FROM $this->table WHERE username = '$username'";
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
