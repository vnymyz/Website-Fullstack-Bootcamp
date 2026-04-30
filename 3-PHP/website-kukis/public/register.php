<?php include '../config/database.php'; ?>

<?php
if (isset($_POST['register'])) {
  $name = $_POST['name'];
  $email = $_POST['email'];
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

  $query = "INSERT INTO users (name, email, password)
            VALUES ('$name', '$email', '$password')";

  mysqli_query($conn, $query);

  header("Location: login.php");
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Register</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex items-center justify-center h-screen bg-gray-100">

<form method="POST" class="bg-white p-6 rounded shadow w-80">

  <h2 class="text-xl font-bold mb-4 text-center">Register</h2>

  <input type="text" name="name" placeholder="Name"
    class="w-full border p-2 mb-3 rounded">

  <input type="email" name="email" placeholder="Email"
    class="w-full border p-2 mb-3 rounded">

  <input type="password" name="password" placeholder="Password"
    class="w-full border p-2 mb-3 rounded">

  <button name="register"
    class="w-full bg-purple-600 text-white py-2 rounded">
    Register
  </button>

</form>

</body>
</html>