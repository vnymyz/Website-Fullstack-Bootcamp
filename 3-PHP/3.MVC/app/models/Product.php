<?php

require_once __DIR__ . '/../../config/database.php';

class Product
{
    private $conn;

    // connect database automatically
    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->connect();
    }

    // get all products
    public function getAllProducts()
    {
        $query = "SELECT * FROM products ORDER BY id DESC";

        $result = mysqli_query($this->conn, $query);

        return $result;
    }
}