<?php

require_once dirname(__FILE__) . '/../DbTable.php';

class Facility extends DbTable
{
    function __construct()
    {
        $this->table = 'facility';
    }

    function updateImageLocation($facility_id, $image_location)
    {
        $this->connect();

        $sql = "UPDATE $this->table SET image_location='$image_location' WHERE facility_id=$facility_id";
        $query = $this->conn->query($sql);

        $this->conn->close();
        return $query;
    }

    function getAll()
    {
        $this->connect();

        $sql = "SELECT * FROM $this->table";
        $query = $this->conn->query($sql);
        $data = array();
        while ($row = $query->fetch_array(MYSQLI_ASSOC)) {
            array_push($data, $row);
        }

        $this->conn->close();
        return $data;
    }
}
