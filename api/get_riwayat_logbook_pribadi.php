<?php
// BUAT FILE BARU: api/get_riwayat_logbook_pribadi.php

session_start();
header('Content-Type: application/json');
include 'koneksi.php';

if (!isset($_SESSION['id_asisten'])) {
    echo json_encode([]); // Kembalikan array kosong jika tidak login
    exit;
}

$id_asisten = $_SESSION['id_asisten'];

$stmt = $conn->prepare("SELECT kegiatan, tanggal FROM logbook WHERE id_asisten = ? ORDER BY tanggal DESC");
$stmt->bind_param("i", $id_asisten);
$stmt->execute();
$result = $stmt->get_result();
$riwayat = [];
while ($row = $result->fetch_assoc()) {
    $riwayat[] = $row;
}

$stmt->close();
$conn->close();

echo json_encode($riwayat);
?>