<?php
require 'auth.php'; // role = admin

$conn = new mysqli(
  "localhost",
  "idnafevn_wauser",
  "z0cxwM[igKg)",
  "idnafevn_amz_idn"
);
if ($conn->connect_error) die("DB Error");

$adminId = (int)$_SESSION['user_id'];
$msg = '';
$error = '';

/* ===== ADMIN DATA ===== */
$stmt = $conn->prepare("SELECT username, password FROM users WHERE id=?");
$stmt->bind_param("i", $adminId);
$stmt->execute();
$admin = $stmt->get_result()->fetch_assoc();
$stmt->close();

/* ===== CHANGE PASSWORD ===== */
if (isset($_POST['change_password'])) {
  $old = $_POST['old_password'] ?? '';
  $new = $_POST['new_password'] ?? '';
  $new2 = $_POST['new_password2'] ?? '';

  if ($new !== $new2) {
    $error = "Password baru tidak sama";
  } elseif (!password_verify($old, $admin['password'])) {
    $error = "Password lama salah";
  } elseif (strlen($new) < 6) {
    $error = "Password minimal 6 karakter";
  } else {
    $hash = password_hash($new, PASSWORD_DEFAULT);
    $s = $conn->prepare("UPDATE users SET password=? WHERE id=?");
    $s->bind_param("si", $hash, $adminId);
    $s->execute();
    $s->close();
    $msg = "Password admin berhasil diganti";
  }
}

/* ===== UPDATE GREETING ===== */
if (isset($_POST['save_greeting'])) {
  $t = trim($_POST['greeting']);
  $s = $conn->prepare("UPDATE settings SET greeting=? WHERE id=1");
  $s->bind_param("s", $t);
  $s->execute();
  $s->close();
}

/* ===== TOGGLE WA ===== */
if (isset($_GET['toggle'])) {
  $id = (int)$_GET['toggle'];
  $conn->query("UPDATE wa_list SET active = IF(active=1,0,1) WHERE id=$id");
  header("Location: admin");
  exit;
}

/* ===== DELETE SINGLE (POST) ===== */
if (isset($_POST['delete_single'])) {
  $id = (int)$_POST['delete_single'];
  $s = $conn->prepare("DELETE FROM wa_list WHERE id=?");
  $s->bind_param("i", $id);
  $s->execute();
  $s->close();
  header("Location: admin");
  exit;
}

/* ===== BULK DELETE ===== */
if (isset($_POST['bulk_delete']) && !empty($_POST['ids'])) {
  $ids = implode(',', array_map('intval', $_POST['ids']));
  $conn->query("DELETE FROM wa_list WHERE id IN ($ids)");
  header("Location: admin");
  exit;
}

/* ===== DATA ===== */
$g = $conn->query("SELECT greeting FROM settings WHERE id=1")->fetch_assoc();
$stat = $conn->query("SELECT SUM(active=1) o, SUM(active=0) f FROM wa_list")->fetch_assoc();

$q = $conn->query("
  SELECT wa_list.*, users.username AS owner
  FROM wa_list
  LEFT JOIN users ON users.id = wa_list.user_id
  ORDER BY wa_list.active DESC, wa_list.id DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard</title>

<?php if($_SERVER['REQUEST_METHOD']==='GET'): ?>
<meta http-equiv="refresh" content="60">
<?php endif; ?>

<style>
:root{
  --bg:#0f172a;--card:#111827;--line:#1f2937;
  --text:#e5e7eb;--muted:#9ca3af;
  --blue:#2563eb;--red:#dc2626;
}
*{box-sizing:border-box}
body{margin:0;background:var(--bg);color:var(--text);font-family:system-ui}
.header{
  position:sticky;top:0;background:var(--card);
  border-bottom:1px solid var(--line);
  padding:16px 24px;
  display:flex;justify-content:space-between;align-items:center;
}
.nav a{color:var(--text);margin-right:16px;text-decoration:none;font-weight:600}
.nav a:hover{color:#93c5fd}
.user-chip{background:#1f2937;padding:8px 14px;border-radius:999px;cursor:pointer}
.container{max-width:1200px;margin:auto;padding:24px}
.cards{display:grid;grid-template-columns:repeat(3,1fr);gap:20px}
.card{background:var(--card);border:1px solid var(--line);border-radius:14px;padding:20px}
.card h3{margin:0;color:var(--muted);font-size:14px}
.card b{font-size:26px}

textarea{
  width:100%;height:90px;background:#020617;color:var(--text);
  border:1px solid var(--line);border-radius:10px;padding:10px
}
button{padding:8px 14px;border-radius:8px;border:0;cursor:pointer;font-weight:600}
.blue{background:var(--blue);color:#fff}
.red{background:var(--red);color:#fff}

table{width:100%;border-collapse:collapse}
th,td{padding:14px;border-bottom:1px solid var(--line);text-align:center}
th{color:var(--muted)}

.badge{padding:5px 12px;border-radius:999px;font-size:12px}
.on{background:#052e16;color:#22c55e}
.off{background:#450a0a;color:#f87171}

.owner{font-size:14px;font-weight:500}
.owner.user{color:var(--text)}
.owner.token{color:var(--muted);font-size:13px}
.owner.empty{color:#6b7280}

.msg{color:#22c55e;font-weight:600}
.error{color:#f87171;font-weight:600}

/* MODAL */
.modal{display:none;position:fixed;inset:0;background:rgba(0,0,0,.6)}
.modal-box{
  background:var(--card);border:1px solid var(--line);
  width:320px;margin:10% auto;padding:22px;border-radius:14px
}
.admin-head{display:flex;gap:12px;align-items:center;margin-bottom:16px}
.avatar{
  width:42px;height:42px;background:#1f2937;border-radius:50%;
  display:flex;align-items:center;justify-content:center;font-size:20px
}
.role{font-size:11px;color:var(--muted);letter-spacing:1px}
.name{font-size:15px;font-weight:600}

.admin-form input{
  width:100%;padding:9px;background:#020617;
  border:1px solid var(--line);border-radius:8px;
  color:var(--text);margin-bottom:8px;font-size:13px
}
.btn-primary{
  width:100%;background:var(--blue);color:#fff;
  border-radius:8px;padding:9px;font-size:14px
}
.divider{height:1px;background:var(--line);margin:14px 0}
.btn-logout{
  display:block;width:100%;text-align:center;
  background:#450a0a;color:#f87171;
  padding:9px;border-radius:8px;font-weight:600;
  text-decoration:none;margin-bottom:8px
}
.btn-close{
  width:100%;background:#1f2937;color:#d1d5db;
  padding:9px;border-radius:8px
}
</style>

<script>
function confirmBulkDelete(){
  if(!confirm("PERINGATAN!\nData yang dihapus tidak bisa dikembalikan.")) return false;
  if(!confirm("KONFIRMASI TERAKHIR!\nYakin hapus SEMUA yang dipilih?")) return false;
  return true;
}
function toggleAll(source){
  document.querySelectorAll('input[name="ids[]"]').forEach(
    cb => cb.checked = source.checked
  );
}
function toggleModal(){
  const m=document.getElementById('userModal');
  m.style.display = (m.style.display==='block') ? 'none' : 'block';
}
</script>
</head>
<body>

<div class="header">
  <div class="nav">
    <a href="admin">Dashboard</a>
    <a href="admin_user">Users</a>
  </div>
  <div class="user-chip" onclick="toggleModal()">👤 <?= htmlspecialchars($admin['username']) ?></div>
</div>

<div class="container">

<?php if($msg):?><div class="msg"><?= $msg ?></div><?php endif;?>
<?php if($error):?><div class="error"><?= $error ?></div><?php endif;?>

<div class="cards">
  <div class="card"><h3>Online</h3><b><?= $stat['o'] ?></b></div>
  <div class="card"><h3>Offline</h3><b><?= $stat['f'] ?></b></div>
  <div class="card"><h3>Total</h3><b><?= $stat['o']+$stat['f'] ?></b></div>
</div>

<br>

<div class="card">
<h3>Global Greeting</h3>
<form method="POST">
<textarea name="greeting"><?= htmlspecialchars($g['greeting']??'') ?></textarea><br><br>
<button class="blue" name="save_greeting">Simpan</button>
</form>
</div>

<br>

<form method="POST" onsubmit="return confirmBulkDelete();">
<div class="card">
<h3>WhatsApp Router</h3>

<table>
<tr>
<th><input type="checkbox" onclick="toggleAll(this)"></th>
<th>ID</th><th>Nomor</th><th>Pemilik</th><th>Status</th><th>Hit</th><th>Aksi</th>
</tr>

<?php while($r=$q->fetch_assoc()): ?>
<tr>
<td><input type="checkbox" name="ids[]" value="<?= $r['id'] ?>"></td>
<td><?= $r['id'] ?></td>
<td><?= htmlspecialchars($r['phone']) ?></td>
<td>
<?php if($r['owner']): ?>
  <span class="owner user"><?= htmlspecialchars($r['owner']) ?></span>
<?php elseif($r['staff_token']): ?>
  <span class="owner token"><?= htmlspecialchars($r['staff_token']) ?></span>
<?php else: ?>
  <span class="owner empty">-</span>
<?php endif; ?>
</td>
<td>
<a href="?toggle=<?= $r['id'] ?>">
<span class="badge <?= $r['active']?'on':'off' ?>">
<?= $r['active']?'ON':'OFF' ?>
</span>
</a>
</td>
<td><?= $r['hit'] ?></td>
<td>
<form method="POST" style="display:inline" onsubmit="return confirm('Yakin hapus nomor ini?')">
  <button class="red" name="delete_single" value="<?= $r['id'] ?>">Hapus</button>
</form>
</td>
</tr>
<?php endwhile; ?>

</table>

<br>
<button class="red" name="bulk_delete">Hapus Terpilih</button>
</div>
</form>

</div>

<!-- ADMIN MODAL -->
<div class="modal" id="userModal">
  <div class="modal-box">
    <div class="admin-head">
      <div class="avatar">👤</div>
      <div>
        <div class="role">ADMIN</div>
        <div class="name"><?= htmlspecialchars($admin['username']) ?></div>
      </div>
    </div>

    <form method="POST" class="admin-form">
      <input type="password" name="old_password" placeholder="Password lama" required>
      <input type="password" name="new_password" placeholder="Password baru" required>
      <input type="password" name="new_password2" placeholder="Ulangi password baru" required>
      <button class="btn-primary" name="change_password">Ganti Password</button>
    </form>

    <div class="divider"></div>

    <a href="logout" class="btn-logout">Logout</a>
    <button onclick="toggleModal()" class="btn-close">Tutup</button>
  </div>
</div>

</body>
</html>
