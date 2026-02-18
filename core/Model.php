<?php
require_once "config/database.php";

class Model {
    protected $db;
    public function __construct() {
        global $conn;
        $this->db = $conn;
    }
}
