<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header('Content-Type: application/json'); // Penting untuk memberitahu klien bahwa respons adalah JSON

include 'koneksi.php'; // Pastikan path ke file koneksi.php sudah benar

$response = [];
$stmt = null; // Inisialisasi $stmt sebagai null

try {
    // KOREKSI UTAMA DI SINI:
    // Hanya pilih kolom yang pasti ada di tabel 'logbook' Anda.
    // Berdasarkan error, 'waktu_mulai', 'waktu_selesai', 'penanggung_jawab' tidak ada.
    // Kita akan menggunakan 'nama_user' yang ada di database Anda.
    $sql = "SELECT id_logbook AS id, tanggal, kegiatan, nama_user FROM logbook ORDER BY tanggal DESC, id_logbook DESC";

    $stmt = $conn->prepare($sql);
    
    // Periksa apakah prepare berhasil
    if (!$stmt) {
        throw new Exception("SQL prepare gagal: " . $conn->error);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $logbook_history = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $logbook_history[] = $row; // Tambahkan setiap baris ke array
        }
    }
    
    echo json_encode($logbook_history); // Encode SELURUH ARRAY sebagai JSON

} catch (Exception $e) {
    // Tangani kesalahan jika ada masalah pada query atau koneksi database
    $response = ["status" => "error", "pesan" => "Terjadi kesalahan pada server: " . $e->getMessage()];
    echo json_encode($response);
} finally {
    // Pastikan $stmt dan $conn ditutup hanya jika sudah diinisialisasi
    if ($stmt !== null) {
        $stmt->close();
    }
    if ($conn !== null) {
        $conn->close();
    }
}
?>