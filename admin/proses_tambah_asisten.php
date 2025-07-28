<?php
// admin/proses_tambah_asisten.php

session_start();
if (!isset($_SESSION['admin_loggedin'])) {
    header('Location: ../login_admin.php');
    exit;
}

include '../api/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil semua data dari form
    $nama = $_POST['nama'];
    $nim = $_POST['nim'];
    $angkatan = $_POST['angkatan'];
    $password = $_POST['password'];

    if (empty($nama) || empty($nim) || empty($angkatan) || empty($password)) {
        die("Error: Semua field harus diisi.");
    }

    // HASH PASSWORD SEBELUM DISIMPAN
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Siapkan statement SQL (tanpa kolom username)
    $stmt = $conn->prepare("INSERT INTO asisten (nama, nim, angkatan, password) VALUES (?, ?, ?, ?)");
    
    // Bind parameter (s = string, i = integer)
    $stmt->bind_param("ssis", $nama, $nim, $angkatan, $hashed_password);

    if ($stmt->execute()) {
        header('Location: kelola_asisten.php');
        exit();
    } else {
        // Cek jika ada error duplikat NIM
        if ($conn->errno == 1062) { // 1062 adalah kode error untuk duplicate entry
            die("Error: NIM/Username '" . htmlspecialchars($nim) . "' sudah terdaftar. Silakan gunakan NIM lain.");
        } else {
            echo "Error: " . $stmt->error;
        }
    }

    $stmt->close();
    $conn->close();

} else {
    header('Location: kelola_asisten.php');
    exit();
}
?>
