<?php
session_start();
include '../config/database.php';

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user;

        header("Location: ../dashboard.php");
    } else {
        echo "Login failed!";
    }
}
?>

<h2>Login</h2>
<form method="POST">
    <input type="email" name="email"><br>
    <input type="password" name="password"><br>
    <button name="login">Login</button>
</form>