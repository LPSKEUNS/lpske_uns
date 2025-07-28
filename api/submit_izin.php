<?php
// api/submit_izin.php

// Header yang diperlukan
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

// Handle pre-flight request (OPTIONS)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

// Sertakan koneksi
include 'koneksi.php'; // Pastikan nama file ini benar

// Cek koneksi
if (!isset($conn)) { // Ganti $conn dengan variabel koneksi Anda, misal $koneksi
    echo json_encode(['status' => 'error', 'message' => 'Koneksi database gagal.']);
    exit();
}

// Pastikan request adalah POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // --- INI BAGIAN YANG DIPERBAIKI ---
    // Ambil data dari $_POST karena form mengirimkan FormData, bukan JSON
    $nama_pengaju = isset($_POST['nama_pengaju']) ? trim($_POST['nama_pengaju']) : '';
    $judul_penelitian = isset($_POST['judul_penelitian']) ? trim($_POST['judul_penelitian']) : '';
    $deskripsi = isset($_POST['deskripsi']) ? trim($_POST['deskripsi']) : '';
    // --- AKHIR PERBAIKAN ---

    // Validasi input tidak boleh kosong
    if (empty($nama_pengaju) || empty($judul_penelitian) || empty($deskripsi)) {
        echo json_encode(['status' => 'error', 'message' => 'Semua field harus diisi.']);
        exit();
    }

    // Status awal adalah 'Diajukan'
    $status_izin = 'Diajukan';

    try {
        // Ganti $conn dengan variabel koneksi Anda ($koneksi)
        // Sesuaikan query dengan kolom tabel Anda
        $stmt = $conn->prepare("INSERT INTO izin (judul_penelitian, deskripsi, nama_pengaju, tanggal_pengajuan, status) VALUES (?, ?, ?, NOW(), ?)");
        
        if (!$stmt) {
            throw new Exception("Prepare statement gagal: " . $conn->error);
        }

        // Bind parameter ke statement (sss = 3 string, s = 1 string)
        $stmt->bind_param("ssss", $judul_penelitian, $deskripsi, $nama_pengaju, $status_izin);
        
        if (!$stmt->execute()) {
            throw new Exception("Execute statement gagal: " . $stmt->error);
        }
        $stmt->close();

        // Ganti 'pesan' menjadi 'message' agar konsisten dengan JavaScript di index.html
        echo json_encode(['status' => 'success', 'message' => 'Pengajuan izin berhasil dikirim.']);

    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal mengajukan izin: ' . $e->getMessage()]);
    } finally {
        if ($conn) { // Ganti $conn dengan variabel koneksi Anda
            $conn->close();
        }
    }

} else {
    echo json_encode(['status' => 'error', 'message' => 'Metode request tidak valid.']);
}
?>
