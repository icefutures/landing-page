<?php
session_start();

// ===== KONEKSI DATABASE =====
$conn = new mysqli(
  "localhost",
  "idnafevn_wauser",
  "z0cxwM[igKg)",
  "idnafevn_amz_idn"
);

if ($conn->connect_error) {
  die("DB Error");
}

$error = '';

// ===== PROSES LOGIN =====
if (isset($_POST['login'])) {
  $username = trim($_POST['username']);
  $password = $_POST['password'];

  $stmt = $conn->prepare("
    SELECT id, password, role
    FROM users
    WHERE username = ? AND active = 1
    LIMIT 1
  ");
  $stmt->bind_param("s", $username);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    if (password_verify($password, $user['password'])) {
      // ===== SET SESSION =====
      $_SESSION['user_id'] = $user['id'];
      $_SESSION['role']    = $user['role'];

      // ===== REDIRECT SESUAI ROLE =====
      if ($user['role'] === 'admin') {
        header("Location: admin.php");
      } else {
        header("Location: staffpanel.php");
      }
      exit;
    }
  }

  $error = "Username atau password salah";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Login</title>
<style>
body{
  background:#0b0b0b;
  color:#ddd;
  font-family:Arial, Helvetica, sans-serif;
}
.login-box{
  max-width:320px;
  margin:100px auto;
  padding:20px;
  background:#111;
  border-radius:6px;
}
h2{
  text-align:center;
  margin-bottom:15px;
}
input{
  width:100%;
  padding:10px;
  margin-bottom:10px;
  border:0;
  border-radius:4px;
}
button{
  width:100%;
  padding:10px;
  border:0;
  border-radius:4px;
  background:#2563eb;
  color:#fff;
  cursor:pointer;
}
.error{
  color:#dc2626;
  text-align:center;
  margin-bottom:10px;
}
small{
  color:#888;
  display:block;
  text-align:center;
  margin-top:10px;
}
</style>
</head>
<body>

<div class="login-box">
  <h2>Login</h2>

  <?php if ($error): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST">
    <input type="text" name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit" name="login">Login</button>
  </form>

  <small>Authorized access only</small>
</div>

</body>
</html>
