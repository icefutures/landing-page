<?php
// Include file koneksi database
include 'db_connection.php';

// Default query untuk mengambil seluruh data
$query = "SELECT * FROM clicks";
$params = [];

// Jika filter tanggal diterapkan
if (isset($_POST['filter_date'])) {
    $date_range = $_POST['date_range'] ?? null;

    if ($date_range) {
        $dates = explode(" - ", $date_range);
        $start_date = date('Y-m-d', strtotime($dates[0]));
        $end_date = date('Y-m-d', strtotime($dates[1]));

        $query = "SELECT * FROM clicks WHERE DATE(timestamp) BETWEEN ? AND ?";
        $params = [$start_date, $end_date];
    }
}

// Menyiapkan query untuk eksekusi
$stmt = $conn->prepare($query);
if ($params) {
    $stmt->bind_param(str_repeat("s", count($params)), ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Date Range Picker CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .sidebar {
            width: 250px;
            background-color: #343a40;
            color: #fff;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            padding: 20px;
            overflow-y: auto;
        }
        .sidebar h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #fff;
        }
        .sidebar ul {
            list-style: none;
            padding: 0;
        }
        .sidebar ul li {
            margin-bottom: 10px;
        }
        .sidebar ul li a {
            color: #fff;
            text-decoration: none;
            padding: 10px;
            display: flex;
            align-items: center;
            border-radius: 5px;
            font-size: 16px;
        }
        .sidebar ul li a.active,
        .sidebar ul li a:hover {
            background-color: #495057;
        }
        .content {
            margin-left: 270px;
            padding: 20px;
        }
        .table {
            margin-top: 20px;
        }
    </style>
</head>
<body>
<div class="sidebar">
    <h4>Admin Dashboard</h4>
    <ul>
        <li><a href="index.php" class="active"><i class="fas fa-table"></i>  Daily Data</a></li>
        <li><a href="duplicate_management.php" class="active"><i class="fas fa-clone"></i>  Duplicate Management</a></li>
    </ul>
</div>

<div class="content">
    <h1>Data Clicks</h1>

    <!-- Form Filter -->
    <form method="POST" class="mb-4">
        <label for="date_range">Pilih Rentang Tanggal:</label>
        <input type="text" name="date_range" id="date_range" class="form-control" placeholder="Klik untuk memilih tanggal" autocomplete="off">
        <button type="submit" name="filter_date" class="btn btn-primary mt-2">Filter</button>
    </form>

    <!-- Tabel Data -->
    <table class="table table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Device ID</th>
                <th>Link Click</th>
                <th>Timestamp</th>
                <th>Landing page</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($data)): ?>
                <?php foreach ($data as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['device_id']) ?></td>
                        <td><?= htmlspecialchars($row['whatsapp_number']) ?></td>
                        <td><?= htmlspecialchars($row['timestamp']) ?></td>
                        <td><?= htmlspecialchars($row['domain']) ?></td>

                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="text-center">Tidak ada data ditemukan</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<!-- Date Range Picker JS -->
<script src="https://cdn.jsdelivr.net/npm/moment/min/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script>
    $(function () {
        // Inisialisasi Date Range Picker
        $('#date_range').daterangepicker({
            locale: {
                format: 'YYYY-MM-DD',
                separator: " - ",
                applyLabel: "Terapkan",
                cancelLabel: "Batal",
                daysOfWeek: ["Mg", "Sn", "Sl", "Rb", "Km", "Jm", "Sb"],
                monthNames: [
                    "Januari", "Februari", "Maret", "April", "Mei", "Juni",
                    "Juli", "Agustus", "September", "Oktober", "November", "Desember"
                ],
                firstDay: 1
            }
        });

        // Sidebar Active State
        const currentLocation = window.location.href;
        document.querySelectorAll('.sidebar ul li a').forEach(link => {
            if (link.href === currentLocation) {
                link.classList.add('active');
            }
        });
    });
</script>
</body>
</html>
