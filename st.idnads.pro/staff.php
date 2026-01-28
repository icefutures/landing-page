<?php
$conn = new mysqli(
  "localhost",
  "idnafevn_wauser",
  "z0cxwM[igKg)",
  "idnafevn_amz_idn"
);

if ($conn->connect_error) {
  die("DB Error");
}

$token = $_GET['token'] ?? '';
if (!$token) {
  die("Token tidak valid");
}

$error = '';

// ================== AMBIL DATA WA MILIK STAFF ==================
$stmt = $conn->prepare("
  SELECT * FROM wa_list
  WHERE staff_token = ?
  ORDER BY id DESC
");
$stmt->bind_param("s", $token);
$stmt->execute();
$list = $stmt->get_result();
$stmt->close();

// ================== TAMBAH WA ==================
if (isset($_POST['add'])) {
  $phone = preg_replace('/[^0-9]/', '', $_POST['phone']);

  // VALIDASI HARUS DIAWALI 628
  if (!preg_match('/^628[0-9]{7,13}$/', $phone)) {
    $error = "Nomor WA harus diawali 628 (contoh: 62812xxxxxxx)";
  } else {
    $stmt = $conn->prepare("
      INSERT INTO wa_list (phone, active, staff_token)
      VALUES (?, 1, ?)
    ");
    $stmt->bind_param("ss", $phone, $token);
    $stmt->execute();
    $stmt->close();

    header("Location: staff.php?token=$token");
    exit;
  }
}

// ================== TOGGLE ON / OFF ==================
if (isset($_GET['toggle'])) {
  $id = intval($_GET['toggle']);

  $stmt = $conn->prepare("
    UPDATE wa_list
    SET active = IF(active=1,0,1)
    WHERE id = ? AND staff_token = ?
  ");
  $stmt->bind_param("is", $id, $token);
  $stmt->execute();
  $stmt->close();

  header("Location: staff.php?token=$token");
  exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Panel Staff WA</title>
<style>
body{
  background:#0b0b0b;
  color:#ddd;
  font-family:Arial, Helvetica, sans-serif;
}
h2{
  margin-bottom:10px;
}
input{
  padding:8px;
  border-radius:4px;
  border:0;
  width:240px;
}
button{
  padding:8px 14px;
  border-radius:4px;
  border:0;
  cursor:pointer;
}
.add{
  background:#2563eb;
  color:#fff;
}
.on{
  background:#16a34a;
  color:#fff;
}
.off{
  background:#dc2626;
  color:#fff;
}
table{
  width:100%;
  border-collapse:collapse;
  margin-top:20px;
}
th,td{
  padding:10px;
  border-bottom:1px solid #333;
  text-align:center;
}
.error{
  color:#dc2626;
  font-weight:bold;
  margin-bottom:10px;
}
small{
  color:#888;
}
</style>
</head>
<body>

<h2>Panel Staff WhatsApp</h2>

<!-- ERROR MESSAGE -->
<?php if (!empty($error)): ?>
  <div class="error">
    <?= htmlspecialchars($error) ?>
  </div>
<?php endif; ?>

<!-- FORM TAMBAH WA -->
<form method="POST">
  <input
    type="text"
    name="phone"
    placeholder="628xxxxxxxxxx"
    pattern="628[0-9]{7,13}"
    title="Nomor WA harus diawali 628 (contoh: 62812xxxxxxx)"
    required
  >
  <button type="submit" name="add" class="add">
    Tambah WA
  </button>
</form>

<small>
• Gunakan format <b>628xxxxxxxxxx</b><br>
• Tidak boleh pakai 08 / +62
</small>

<!-- LIST WA -->
<table>
<tr>
  <th>ID</th>
  <th>Nomor WA</th>
  <th>Status</th>
  <th>Hit</th>
</tr>

<?php if ($list->num_rows == 0): ?>
<tr>
  <td colspan="4"><small>Belum ada nomor WA</small></td>
</tr>
<?php endif; ?>

<?php while($r = $list->fetch_assoc()): ?>
<tr>
  <td><?= $r['id'] ?></td>
  <td><?= htmlspecialchars($r['phone']) ?></td>
  <td>
    <a href="?toggle=<?= $r['id'] ?>&token=<?= $token ?>">
      <button class="<?= $r['active'] ? 'on' : 'off' ?>">
        <?= $r['active'] ? 'ON' : 'OFF' ?>
      </button>
    </a>
  </td>
  <td><?= $r['hit'] ?></td>
</tr>
<?php endwhile; ?>
</table>

</body>
</html>