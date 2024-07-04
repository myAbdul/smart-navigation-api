<?php

/**
 * Handling database connection
 */
class DbConnect {

    private $conn;

    function __construct() {
        
    }

    /**
     * Establishing database connection
     *
     * @return database connection handler
     */
    function connect() {
        include_once dirname(__FILE__) . '/Config.php';

        $connInfo = array(
            "host" => DB_HOST,
            "database" => DB_NAME,
            "username" => DB_USERNAME,
            "pwd" => DB_PASSWORD
        );

        // Connecting to mysql database
        $this->conn = new mysqli($connInfo["host"],$connInfo["username"],$connInfo["pwd"],$connInfo["database"]);
        if ($this->conn->connect_errno) {
             echo "Connection failed" . "<br/>";
        }
        // Check for database connection error
        if (mysqli_connect_errno ()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error ();
        }
        // returing connection resource
        return $this->conn;
    }

}

?>
