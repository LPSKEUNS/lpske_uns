<?php
// Ini adalah file backend PHP yang akan diakses oleh JavaScript (AJAX)
// untuk mengambil data peminjaman aktif dari database.

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

include 'koneksi.php';

// Pastikan variabel koneksi database ($conn) sudah terdefinisi.
if (!isset($conn)) {
    echo json_encode(['status' => 'error', 'pesan' => 'Koneksi database tidak tersedia.']);
    exit();
}

try {
    // Query untuk mengambil data peminjaman yang statusnya 'Dipinjam'
    // JOIN dengan tabel inventory untuk mendapatkan nama_alat
    // --- PERUBAHAN DI SINI: WHERE status = 'Dipinjam' ---
    $sql = "SELECT p.id_pinjam, p.id_alat, p.nama_peminjam, p.keperluan, p.status, i.nama_alat 
            FROM peminjaman p
            JOIN inventory i ON p.id_alat = i.id_alat
            WHERE p.status = 'Dipinjam'
            ORDER BY p.tanggal_pinjam DESC";
    $result = $conn->query($sql);

    $peminjaman_data = [];
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $peminjaman_data[] = [
                'id_pinjam' => $row['id_pinjam'],
                'id_alat' => $row['id_alat'],
                'nama_peminjam' => $row['nama_peminjam'],
                'keperluan' => $row['keperluan'],
                'nama_alat' => $row['nama_alat'],
                'status' => $row['status'] // Langsung gunakan status dari tabel peminjaman
            ];
        }
    }
    
    echo json_encode($peminjaman_data);

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'pesan' => 'Gagal mengambil data peminjaman: ' . $e->getMessage()]);
} finally {
    if ($conn) {
        $conn->close();
    }
}
?>
