<?php
$conn = mysqli_connect("localhost", "root", "", "auth_system");

if (!$conn) {
    die("Connection failed");
}