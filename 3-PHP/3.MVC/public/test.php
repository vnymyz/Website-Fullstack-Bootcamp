<?php

require_once '../config/database.php';

// create object
$database = new Database();

// connect database
$conn = $database->connect();

// test result
if ($conn) {
    echo "Database Connected Successfully!";
}