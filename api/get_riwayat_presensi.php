<?php
// api/get_riwayat_presensi.php

session_start();
header('Content-Type: application/json');
include 'koneksi.php';

// Pastikan asisten sudah login
if (!isset($_SESSION['id_asisten'])) {
    echo json_encode(['status' => 'error', 'pesan' => 'Akses ditolak.']);
    exit;
}

// Ambil ID asisten dari session
$id_asisten = $_SESSION['id_asisten'];

// Ambil semua data presensi untuk asisten yang sedang login, diurutkan dari yang terbaru
$stmt = $conn->prepare("SELECT tanggal, jam_masuk, jam_keluar, status FROM presensi WHERE id_asisten = ? ORDER BY tanggal DESC, jam_masuk DESC");
$stmt->bind_param("i", $id_asisten);
$stmt->execute();
$result = $stmt->get_result();

$riwayat = [];
while ($row = $result->fetch_assoc()) {
    $riwayat[] = $row;
}

$stmt->close();
$conn->close();

// Kembalikan data dalam format JSON
echo json_encode($riwayat);
?>
