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
}
