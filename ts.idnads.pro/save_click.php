<?php
// Atur log error ke file error.log
ini_set('log_errors', 'On');
ini_set('error_log', __DIR__ . '/error.log'); // Lokasi file log di folder yang sama dengan file ini

date_default_timezone_set('Asia/Jakarta');
// Include file koneksi
include 'db_connection.php';

// Debug koneksi database
if ($conn->connect_error) {
    error_log("Koneksi database gagal: " . $conn->connect_error);
    die("Koneksi database gagal: " . $conn->connect_error);
}

// Ambil data JSON dari permintaan POST
$data = json_decode(file_get_contents('php://input'), true);

// Debug: Log data yang diterima
error_log("Data yang diterima: " . print_r($data, true));

// Validasi data
if (!isset($data['deviceId']) || !isset($data['whatsappNumber'])) {
    error_log("Data tidak lengkap: " . print_r($data, true));
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']);
    exit;
}

// Ambil nilai dari data JSON
$device_id = $data['deviceId'];
$whatsapp_number = $data['whatsappNumber'];
$domain = $data['domain'];
$timestamp = date('Y-m-d H:i:s'); // Waktu saat ini

// Query untuk menyimpan data
$stmt = $conn->prepare("INSERT INTO clicks (device_id, whatsapp_number, timestamp,domain) VALUES (?, ?, ?,?)");

// Debug: Log jika query gagal dipersiapkan
if (!$stmt) {
    error_log("Query preparation error: " . $conn->error);
    echo json_encode(['status' => 'error', 'message' => 'Gagal mempersiapkan query']);
    exit;
}

$stmt->bind_param("ssss", $device_id, $whatsapp_number, $timestamp,$domain);

if ($stmt->execute()) {
    error_log("Data berhasil disimpan: DeviceID=$device_id, WhatsAppNumber=$whatsapp_number, Timestamp=$timestamp");
    echo json_encode(['status' => 'success', 'message' => 'Data berhasil disimpan']);
} else {
    // Debug error database
    error_log("Database error: " . $stmt->error);
    echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan data']);
}

// Tutup statement
$stmt->close();

// Tutup koneksi database
$conn->close();
?>
