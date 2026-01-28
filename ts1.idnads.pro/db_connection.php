<?php
$host = '127.0.0.1'; // Alamat localhost
$user = 'idnafevn_sands'; // Username default MySQL
$pass = 'QVU!z&=F?)+r'; // Password default MySQL (kosong)
$db_name = 'idnafevn_sands'; // Nama database Anda

// Membuat koneksi ke MySQL
$conn = new mysqli($host, $user, $pass, $db_name);

// Periksa apakah koneksi berhasil
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}
?>
