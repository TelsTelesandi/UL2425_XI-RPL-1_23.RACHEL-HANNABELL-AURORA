<!-- login.php -->
<?php
session_start();
include 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $username = $_POST['username'];
  $password = $_POST['password'];

  $query = mysqli_query($conn, "SELECT * FROM users WHERE username='$username' AND password='$password'");
  $data = mysqli_fetch_assoc($query);

  if ($data) {
    $_SESSION['user_id'] = $data['user_id'];
    $_SESSION['role'] = $data['role'];

    if ($data['role'] == 'admin') {
      header('Location: admin/dashboard.php');
    } else {
      header('Location: user/dashboard.php');
    }
  } else {
    echo "<script>alert('Login gagal!');window.location='login.php';</script>";
  }
}
?>

<!DOCTYPE html>
<html>

<head>
  <title>Login - Aplikasi Peminjaman</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
    }
    .login-container {
      max-width: 400px;
      width: 100%;
    }
    .login-image {
      width: 100%;
      max-width: 250px;
      height: auto;
      margin: 20px auto;
    }
  </style>
</head>

<body class="d-flex justify-content-center align-items-center vh-100">
  <div class="login-container">
    <div class="card p-4 shadow">
      <h3 class="text-center mb-4">Login</h3>
      <img src="assets/img/login.jpg" alt="Login" class="login-image d-block">
      <form method="POST">
        <div class="mb-3">
          <input type="text" name="username" class="form-control" placeholder="Username" required>
        </div>
        <div class="mb-3">
          <input type="password" name="password" class="form-control" placeholder="Password" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Masuk</button>
      </form>
    </div>
  </div>
</body>

</html>