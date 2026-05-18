<?php

// connect to mysql
             // servername, username, password, database
$conn = mysqli_connect("localhost", "root", "", "game");

// check connection
if (!$conn) {
    die("Connection Failed: " . mysqli_connect_error());
}