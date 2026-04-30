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

    header("Location: admin/dashboard.php");
  } else {
    echo "Login failed!";
  }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex items-center justify-center h-screen bg-gray-100">

<form method="POST" class="bg-white p-6 rounded shadow w-80">

  <h2 class="text-xl font-bold mb-4 text-center">Login</h2>

  <input type="email" name="email" placeholder="Email"
    class="w-full border p-2 mb-3 rounded">

  <input type="password" name="password" placeholder="Password"
    class="w-full border p-2 mb-3 rounded">

  <button name="login"
    class="w-full bg-purple-600 text-white py-2 rounded">
    Login
  </button>

</form>

</body>
</html>