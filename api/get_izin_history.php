<?php
session_start();
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

include 'koneksi.php';

if (!isset($conn)) {
    echo json_encode(['status' => 'error', 'pesan' => 'Koneksi database tidak tersedia.']);
    exit();
}

try {
    // Ambil nama pengguna yang login untuk memfilter riwayat izin
    // Jika tidak ada sistem login user, ini akan menampilkan semua izin atau Anda perlu filter berdasarkan nama_pengaju di form
    $nama_pengaju_filter = isset($_SESSION['nama_peminjam_login']) ? $_SESSION['nama_peminjam_login'] : null; 

    $sql = "SELECT id_izin, judul_penelitian, deskripsi, nama_pengaju, tanggal_pengajuan, status 
            FROM izin";
    
    $params = [];
    $types = "";

    // Filter berdasarkan nama_pengaju jika ada yang login
    if ($nama_pengaju_filter) {
        $sql .= " WHERE nama_pengaju = ?";
        $params[] = $nama_pengaju_filter;
        $types .= "s";
    }

    $sql .= " ORDER BY tanggal_pengajuan DESC";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare statement failed: " . $conn->error);
    }

    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    $izin_data = [];
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $izin_data[] = [
                'id_izin' => $row['id_izin'],
                'judul_penelitian' => $row['judul_penelitian'],
                'deskripsi' => $row['deskripsi'],
                'nama_pengaju' => $row['nama_pengaju'],
                'tanggal_pengajuan' => $row['tanggal_pengajuan'],
                'status' => $row['status'] // Langsung gunakan status dari tabel izin
            ];
        }
    }
    
    echo json_encode($izin_data);

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'pesan' => 'Gagal mengambil riwayat izin: ' . $e->getMessage()]);
} finally {
    if ($conn) {
        $conn->close();
    }
}
?>
