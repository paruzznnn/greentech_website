<?php
class Database {
    public $mysqli;

    public function __construct() {
        $this->mysqli = new \mysqli('localhost', 'root', '', 'greentech_db');
        if ($this->mysqli->connect_error) {
            die('Connection failed: ' . $this->mysqli->connect_error);
        }
    }
}
?>
