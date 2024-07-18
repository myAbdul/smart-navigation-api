<?php

require_once dirname(__FILE__) . '/../DbTable.php';

class CampusEvent extends DbTable
{
    function __construct()
    {
        $this->table = 'campus_event';
    }

    function getCampusEvents()
    {
        $this->connect();

        $sql = "SELECT ce.campus_event_id, ce.name, ce.date, ce.time, f.name as facility_name, f.longitude as facility_longitude, f.latitude as facility_latitude FROM campus_event as ce inner join facility as f on ce.facility_id = f.facility_id";
        $query = $this->conn->query($sql);
        $data = array();
        while($row = $query->fetch_array(MYSQLI_ASSOC)) {
            array_push($data, $row);
        }
        
        $this->conn->close();
        return $data;
    }
}
