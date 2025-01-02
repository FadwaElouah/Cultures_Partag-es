<?php
require_once 'config.php';


class Article {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
 
}
