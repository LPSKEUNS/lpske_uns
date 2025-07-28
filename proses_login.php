<?php
// 1. Mulai session
session_start();

// 2. Hubungkan ke database (asumsi Anda punya file db_connect.php)
include 'api/koneksi.php'; 

// 3. Periksa apakah form sudah disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 4. Ambil data dari form
    $username = $_POST['username'];
    $password = $_POST['password'];

    // 5. Query untuk mencari admin berdasarkan username
    // Gunakan prepared statements untuk keamanan dari SQL Injection
    $stmt = $conn->prepare("SELECT id, username, password FROM admin WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // 6. Verifikasi data
    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();
        
        // Verifikasi password yang di-input dengan hash di database
        if (password_verify($password, $admin['password'])) {
            // Jika password cocok, login berhasil
            
            // Simpan informasi admin ke dalam session
            $_SESSION['admin_loggedin'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            
            // Arahkan ke halaman dashboard admin
            header("Location: admin/dashboard.php");
            exit;
            
        } else {
            // Jika password salah
            header("Location: login_admin.php?error=1");
            exit;
        }
        
    } else {
        // Jika username tidak ditemukan
        header("Location: login_admin.php?error=1");
        exit;
    }

    $stmt->close();
    $conn->close();
    
} else {
    // Jika file diakses langsung tanpa submit form
    header("Location: login_admin.php");
    exit;
}
?>