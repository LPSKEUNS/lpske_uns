<?php
// proses_login_asisten.php

session_start();

// =======================================================
// BAGIAN YANG DIPERBAIKI ADA DI SINI
// =======================================================
include 'api/koneksi.php'; // Path diubah menjadi 'api/koneksi.php'
// =======================================================

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Kita menggunakan 'nim' sebagai input username
    $nim = $_POST['nim'];
    $password = $_POST['password'];

    if (empty($nim) || empty($password)) {
        header('Location: login_asisten.php?error=NIM dan password harus diisi');
        exit;
    }

    // Cari asisten berdasarkan NIM
    $stmt = $conn->prepare("SELECT id_asisten, nama, password FROM asisten WHERE nim = ?");
    $stmt->bind_param("s", $nim);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $asisten = $result->fetch_assoc();

        // Verifikasi password yang diinput dengan hash di database
        if (password_verify($password, $asisten['password'])) {
            // Jika password cocok, login berhasil
            
            // Simpan data penting ke session
            $_SESSION['id_asisten'] = $asisten['id_asisten'];
            $_SESSION['nama_asisten'] = $asisten['nama'];
            
            // Arahkan ke dashboard khusus asisten
            header('Location: asisten_dashboard.php');
            exit;
        } else {
            // Jika password salah
            header('Location: login_asisten.php?error=Password salah');
            exit;
        }
    } else {
        // Jika NIM tidak ditemukan
        header('Location: login_asisten.php?error=NIM tidak terdaftar sebagai asisten');
        exit;
    }

    $stmt->close();
    $conn->close();

} else {
    header('Location: login_asisten.php');
    exit;
}
?>
