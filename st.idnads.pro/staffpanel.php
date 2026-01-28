<?php
require 'auth.php';

$conn = new mysqli(
  "localhost",
  "idnafevn_wauser",
  "z0cxwM[igKg)",
  "idnafevn_amz_idn"
);

if ($conn->connect_error) die("DB Error");

$userId = (int)$_SESSION['user_id'];
$msg = '';
$error = '';

$stmt = $conn->prepare("SELECT username, password FROM users WHERE id=?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

/* GANTI PASSWORD */
if (isset($_POST['change_password'])) {
  $old=$_POST['old_password']; $new=$_POST['new_password']; $new2=$_POST['new_password2'];
  if ($new!==$new2) $error="Password baru tidak sama";
  elseif (!password_verify($old,$user['password'])) $error="Password lama salah";
  elseif (strlen($new)<6) $error="Password minimal 6 karakter";
  else {
    $hash=password_hash($new,PASSWORD_DEFAULT);
    $stmt=$conn->prepare("UPDATE users SET password=? WHERE id=?");
    $stmt->bind_param("si",$hash,$userId);
    $stmt->execute(); $stmt->close();
    $msg="Password berhasil diganti";
  }
}

/* TAMBAH WA */
if (isset($_POST['add'])) {
  $phone=preg_replace('/[^0-9]/','',$_POST['phone']);
  if (!preg_match('/^628[0-9]{7,13}$/',$phone)) {
    $error="Nomor WA harus diawali 628";
  } else {
    $cek=$conn->prepare("SELECT id FROM wa_list WHERE phone=?");
    $cek->bind_param("s",$phone); $cek->execute(); $cek->store_result();
    if ($cek->num_rows>0) $error="Nomor WA sudah terdaftar";
    else {
      $stmt=$conn->prepare("INSERT INTO wa_list (phone,active,user_id) VALUES (?,1,?)");
      $stmt->bind_param("si",$phone,$userId);
      $stmt->execute(); $stmt->close();
      header("Location: staffpanel.php"); exit;
    }
    $cek->close();
  }
}

/* TOGGLE */
if (isset($_GET['toggle'])) {
  $id=(int)$_GET['toggle'];
  $stmt=$conn->prepare("UPDATE wa_list SET active=IF(active=1,0,1) WHERE id=? AND user_id=?");
  $stmt->bind_param("ii",$id,$userId);
  $stmt->execute(); $stmt->close();
  header("Location: staffpanel.php"); exit;
}

$stmt=$conn->prepare("SELECT * FROM wa_list WHERE user_id=? ORDER BY id DESC");
$stmt->bind_param("i",$userId);
$stmt->execute();
$list=$stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Staff Panel</title>
<style>
:root{
  --bg:#0f172a;
  --card:#111827;
  --line:#1f2937;
  --text:#e5e7eb;
  --muted:#9ca3af;
  --blue:#2563eb;
  --green:#16a34a;
  --red:#dc2626;
}
*{box-sizing:border-box}
body{
  margin:0;
  background:var(--bg);
  color:var(--text);
  font-family:system-ui,-apple-system,Segoe UI,Roboto;
}
.header{
  position:sticky;top:0;
  background:var(--card);
  border-bottom:1px solid var(--line);
  padding:16px 24px;
  display:flex;
  justify-content:space-between;
  align-items:center;
}
.user-chip{
  background:#1f2937;
  padding:8px 14px;
  border-radius:999px;
  cursor:pointer;
  font-weight:600;
}
.container{
  max-width:900px;
  margin:30px auto;
  padding:0 20px;
}
.card{
  background:var(--card);
  border:1px solid var(--line);
  border-radius:12px;
  padding:20px;
  margin-bottom:24px;
}
input{
  width:260px;
  padding:10px;
  background:#020617;
  border:1px solid var(--line);
  border-radius:8px;
  color:var(--text);
}
button{
  border:0;
  padding:10px 16px;
  border-radius:8px;
  cursor:pointer;
  font-weight:600;
}
.btn-blue{background:var(--blue);color:#fff}
.btn-green{background:var(--green);color:#fff}
.btn-red{background:var(--red);color:#fff}
table{
  width:100%;
  border-collapse:collapse;
}
th,td{
  padding:14px;
  border-bottom:1px solid var(--line);
  text-align:center;
}
th{color:var(--muted);font-weight:600}
.status{
  padding:6px 14px;
  border-radius:999px;
  font-size:13px;
}
.on{background:#052e16;color:#22c55e}
.off{background:#450a0a;color:#f87171}
.msg{color:#22c55e;font-weight:600;margin-bottom:10px}
.error{color:#f87171;font-weight:600;margin-bottom:10px}

/* MODAL */
.modal{
  display:none;
  position:fixed;
  inset:0;
  background:rgba(0,0,0,.6);
}
.modal-box{
  background:var(--card);
  width:340px;
  margin:10% auto;
  padding:24px;
  border-radius:14px;
  border:1px solid var(--line);
}
.modal-box input{width:100%;margin-bottom:10px}
</style>
</head>
<body>

<div class="header">
  <h2>Staff Panel</h2>
  <div class="user-chip" onclick="toggleModal()">👤 <?= htmlspecialchars($user['username']) ?></div>
</div>

<div class="container">

<?php if($msg):?><div class="msg"><?= $msg ?></div><?php endif;?>
<?php if($error):?><div class="error"><?= $error ?></div><?php endif;?>

<div class="card">
<h3>Tambah WhatsApp</h3>
<form method="POST">
  <input name="phone" placeholder="628xxxxxxxxxx" required>
  <button class="btn-blue" name="add">Tambah</button>
</form>
</div>

<div class="card">
<h3>WhatsApp Saya</h3>
<table>
<tr><th>ID</th><th>Nomor</th><th>Status</th><th>Hit</th></tr>
<?php if($list->num_rows==0):?>
<tr><td colspan="4" style="color:var(--muted)">Belum ada WA</td></tr>
<?php endif;?>
<?php while($r=$list->fetch_assoc()):?>
<tr>
<td><?= $r['id']?></td>
<td><?= htmlspecialchars($r['phone'])?></td>
<td>
<a href="?toggle=<?= $r['id']?>">
<span class="status <?= $r['active']?'on':'off' ?>">
<?= $r['active']?'ON':'OFF' ?>
</span>
</a>
</td>
<td><?= $r['hit']?></td>
</tr>
<?php endwhile;?>
</table>
</div>

</div>

<div class="modal" id="userModal">
<div class="modal-box">
<h3>👤 Akun</h3>
<p><b><?= htmlspecialchars($user['username']) ?></b></p>
<hr>
<form method="POST">
<input type="password" name="old_password" placeholder="Password lama" required>
<input type="password" name="new_password" placeholder="Password baru" required>
<input type="password" name="new_password2" placeholder="Ulangi password baru" required>
<button class="btn-blue" name="change_password">Ganti Password</button>
</form>
<hr>
<a href="logout.php"><button class="btn-red" style="width:100%">Logout</button></a>
<button onclick="toggleModal()" style="width:100%;margin-top:10px">Tutup</button>
</div>
</div>

<script>
function toggleModal(){
  const m=document.getElementById('userModal');
  m.style.display=m.style.display==='block'?'none':'block';
}
</script>

</body>
</html>
