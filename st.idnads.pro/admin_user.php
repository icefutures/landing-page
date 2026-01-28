<?php
require 'auth.php';

// pastikan admin
if ($_SESSION['role'] !== 'admin') {
  die('Forbidden');
}

$conn = new mysqli(
  "localhost",
  "idnafevn_wauser",
  "z0cxwM[igKg)",
  "idnafevn_amz_idn"
);

$msg = '';

// ===== TAMBAH STAFF =====
if (isset($_POST['add_staff'])) {
  $username = trim($_POST['username']);
  $password = $_POST['password'];

  if ($username && $password) {
    $hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("
      INSERT INTO users (username, password, role)
      VALUES (?, ?, 'staff')
    ");
    $stmt->bind_param("ss", $username, $hash);

    if ($stmt->execute()) {
      $msg = "Staff berhasil ditambahkan";
    } else {
      $msg = "Username sudah dipakai";
    }
    $stmt->close();
  }
}

// ===== GANTI PASSWORD ADMIN =====
if (isset($_POST['change_pass'])) {
  $newpass = $_POST['new_password'];

  if ($newpass) {
    $hash = password_hash($newpass, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("
      UPDATE users SET password=?
      WHERE id=?
    ");
    $stmt->bind_param("si", $hash, $_SESSION['user_id']);
    $stmt->execute();
    $stmt->close();

    $msg = "Password admin berhasil diganti";
  }
}

// ===== LIST USER =====
$users = $conn->query("
  SELECT id, username, role, active
  FROM users
  ORDER BY role, id DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Manajemen User</title>

<style>
:root{
  --bg:#0f172a;
  --card:#111827;
  --line:#1f2937;
  --text:#e5e7eb;
  --muted:#9ca3af;
  --blue:#2563eb;
  --green:#16a34a;
}
*{box-sizing:border-box}
body{
  margin:0;
  background:var(--bg);
  color:var(--text);
  font-family:system-ui,-apple-system,Segoe UI,Roboto;
}
.header{
  position:sticky;
  top:0;
  background:var(--card);
  border-bottom:1px solid var(--line);
  padding:16px 24px;
  display:flex;
  justify-content:space-between;
  align-items:center;
}
.header a{
  color:var(--text);
  text-decoration:none;
  font-weight:600;
}
.header a:hover{color:#93c5fd}

.container{
  max-width:900px;
  margin:30px auto;
  padding:0 20px;
}
.card{
  background:var(--card);
  border:1px solid var(--line);
  border-radius:14px;
  padding:20px;
  margin-bottom:24px;
}
.card h3{
  margin-top:0;
  margin-bottom:14px;
  font-size:16px;
}
input{
  width:100%;
  padding:10px;
  background:#020617;
  border:1px solid var(--line);
  border-radius:8px;
  color:var(--text);
  margin-bottom:10px;
}
button{
  padding:10px;
  border-radius:8px;
  border:0;
  cursor:pointer;
  font-weight:600;
}
.btn-blue{background:var(--blue);color:#fff}
.msg{
  background:#052e16;
  color:#22c55e;
  padding:10px;
  border-radius:8px;
  margin-bottom:20px;
}
table{
  width:100%;
  border-collapse:collapse;
}
th,td{
  padding:12px;
  border-bottom:1px solid var(--line);
  text-align:left;
}
th{
  color:var(--muted);
  font-weight:600;
}
.badge{
  padding:4px 10px;
  border-radius:999px;
  font-size:12px;
}
.active{background:#052e16;color:#22c55e}
.inactive{background:#450a0a;color:#f87171}
.role-admin{color:#93c5fd;font-weight:600}
.role-staff{color:#eab308;font-weight:600}
</style>
</head>
<body>

<div class="header">
  <div>
    <a href="admin.php">← Dashboard</a>
  </div>
  <div>
    Manajemen User
  </div>
</div>

<div class="container">

<?php if($msg): ?>
<div class="msg"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<div class="card">
<h3>➕ Tambah Staff</h3>
<form method="POST">
  <input name="username" placeholder="Username staff" required>
  <input type="password" name="password" placeholder="Password staff" required>
  <button class="btn-blue" name="add_staff">Tambah Staff</button>
</form>
</div>

<div class="card">
<h3>🔐 Ganti Password Admin</h3>
<form method="POST">
  <input type="password" name="new_password" placeholder="Password baru" required>
  <button class="btn-blue" name="change_pass">Ganti Password</button>
</form>
</div>

<div class="card">
<h3>📋 Daftar User</h3>
<table>
<tr>
  <th>ID</th>
  <th>Username</th>
  <th>Role</th>
  <th>Status</th>
</tr>

<?php while($u = $users->fetch_assoc()): ?>
<tr>
  <td><?= $u['id'] ?></td>
  <td><?= htmlspecialchars($u['username']) ?></td>
  <td>
    <span class="<?= $u['role']=='admin'?'role-admin':'role-staff' ?>">
      <?= strtoupper($u['role']) ?>
    </span>
  </td>
  <td>
    <span class="badge <?= $u['active']?'active':'inactive' ?>">
      <?= $u['active']?'AKTIF':'NONAKTIF' ?>
    </span>
  </td>
</tr>
<?php endwhile; ?>

</table>
</div>

</div>
</body>
</html>
