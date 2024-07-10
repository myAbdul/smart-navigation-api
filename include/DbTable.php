<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once dirname(__FILE__) . '/DbConnect.php';

class DbTable {
    protected $conn;
    protected $table;

    // Creating Sale
    
    public function connect() {
        $db = new DbConnect();
        $this->conn = $db->connect();
    }
    
    public function fetchAll($where = null) {  
        $this->connect();        
        $sql = "SELECT * FROM $this->table";
        $query = $this->conn->query($sql);
        $data = array();
        while($row = $query->fetch_array(MYSQLI_ASSOC)) {
            array_push($data, $row);
        }
        $this->conn->close();
        return $data;
    }
    
    
    /**
     * Multiple inserts
     * @param array $data
     */
    public function insert($data) {
        $this->connect();        
        $sql = "";
        foreach($data as $key => $val) {
            if(is_array($val)) {
                $sql .= "INSERT INTO $this->table (";
                $i = 0;
                foreach ($val as $col => $v) {
                    if($i < count($val)-1) {
                        $sql = $sql . $col . ", ";
                    }
                    else {
                        $sql = $sql . $col . ')';
                    }
                    $i++;
                }
                $sql = $sql . " VALUES (";
                $i = 0;
                foreach ($val as $col => $v) {
                    if($i < count($val)-1) {                        
                        $sql = $sql . "'" . $v . "', ";
                    }
                    else {
                        $sql = $sql . "'" . $v . "'";
                    }
                    $i++;
                }
                $sql .= "); \n";
            }
        }
        try {
            $result = $this->conn->query($sql);
        } catch (Exception $ex) {
            $result = false;
        }
        $this->conn->close();
        return $result;
    }
    
    //Single inserts
    public function insertSingle($data) {
        $this->connect();   
        $insert_id = 0;
        $sql = "";
        if(is_array($data)) {
            $sql .= "INSERT INTO $this->table (";
            $i = 0;
            foreach ($data as $col => $v) {
                if($i < count($data)-1) {
                    $sql = $sql . $col . ", ";
                }
                else {
                    $sql = $sql . $col . ')';
                }
                $i++;
            }
            $sql = $sql . " VALUES (";
            $i = 0;
            foreach ($data as $col => $v) {
                if($i < count($data)-1) {                        
                    $sql = $sql . "'" . $v . "', ";
                }
                else {
                    $sql = $sql . "'" . $v . "'";
                }
                $i++;
            }
            $sql .= "); \n";
        }
        try {
            $result = $this->conn->query($sql);
            if(!$result){
                $insert_id = 0;
            } else {
                $insert_id = $this->conn->insert_id;
            }
        } catch (Exception $ex) {
            print($ex);
            $insert_id = 0;
        }
        $this->conn->close();
        return $insert_id;
    }
    
    public function getConn() {
        return $this->conn;
    }
}