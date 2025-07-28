<?php
// GANTI ISI FILE LAMA: api/submit_logbook.php

session_start();
header('Content-Type: application/json');
include 'koneksi.php';

// Keamanan: Pastikan asisten sudah login
if (!isset($_SESSION['id_asisten'], $_SESSION['nama_asisten'])) {
    echo json_encode(['status' => 'error', 'pesan' => 'Akses ditolak. Sesi tidak valid.']);
    exit;
}

$data = json_decode(file_get_contents("php://input"));
$kegiatan = $data->kegiatan ?? '';

if (empty($kegiatan)) {
    echo json_encode(['status' => 'error', 'pesan' => 'Catatan kegiatan tidak boleh kosong.']);
    exit;
}

// Ambil ID dan Nama Asisten dari SESSION
$id_asisten = $_SESSION['id_asisten'];
$nama_asisten = $_SESSION['nama_asisten']; // Ambil nama dari session

// PERBAIKAN: Tambahkan kolom `nama_user` di query
$stmt = $conn->prepare("INSERT INTO logbook (id_asisten, nama_user, kegiatan, tanggal) VALUES (?, ?, ?, NOW())");

// PERBAIKAN: Sesuaikan tipe data dan variabel di bind_param ("iss" -> integer, string, string)
$stmt->bind_param("iss", $id_asisten, $nama_asisten, $kegiatan);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'pesan' => 'Catatan logbook berhasil disimpan.']);
} else {
    echo json_encode(['status' => 'error', 'pesan' => 'Gagal menyimpan catatan: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>