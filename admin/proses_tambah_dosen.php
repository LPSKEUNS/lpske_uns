<?php
// Gatekeeper, session, & koneksi
session_start();
if (!isset($_SESSION['admin_loggedin'])) {
    header('Location: ../login_admin.php');
    exit;
}
include '../api/koneksi.php';

// Cek apakah metode request adalah POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form dan bersihkan
    $nama_lengkap = $_POST['nama_lengkap'];
    $jabatan = $_POST['jabatan'];
    $nip = $_POST['nip'];

    // Gunakan prepared statement untuk keamanan
    // DIUBAH: Dulu ada 4 tanda tanya, sekarang 3
    $stmt = $conn->prepare("INSERT INTO dosen (nama_lengkap, jabatan, nip) VALUES (?, ?, ?)");
    
    // DIUBAH: Dulu "ssss", sekarang "sss" karena hanya ada 3 variabel
    $stmt->bind_param("sss", $nama_lengkap, $jabatan, $nip);

    // Eksekusi query
    if ($stmt->execute()) {
        // Jika berhasil, kembali ke halaman kelola dosen
        header("Location: kelola_dosen.php?status=sukses");
    } else {
        // Jika gagal, kembali dengan pesan error
        header("Location: kelola_dosen.php?status=gagal");
    }

    $stmt->close();
    $conn->close();
}
?>