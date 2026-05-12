<?php

class Database
{
    // database credentials
    private $host = "localhost";
    private $username = "root";
    private $password = "";
    private $database = "beauty_shop";

    // connection variable
    public $conn;

    // connect database
    public function connect()
    {
        $this->conn = mysqli_connect(
            $this->host,
            $this->username,
            $this->password,
            $this->database
        );

        // check connection
        if (!$this->conn) {
            die("Connection Failed: " . mysqli_connect_error());
        }

        return $this->conn;
    }
}