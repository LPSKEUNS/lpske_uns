<?php
// Pastikan header CORS di sini juga!
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
include 'koneksi.php'; // Pastikan path ini benar relatif terhadap file ini

// Ambil tanggal dari parameter GET, jika tidak ada, gunakan tanggal hari ini
$tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : date("Y-m-d");

$sql = "SELECT p.id_presensi, a.nama AS nama_asisten, p.tanggal, p.jam_masuk, p.jam_keluar, p.status
        FROM presensi p
        JOIN asisten a ON p.id_asisten = a.id_asisten
        WHERE p.tanggal = ? ORDER BY p.jam_masuk ASC";
$stmt = $conn->prepare($sql);

// Periksa apakah prepare() berhasil
if ($stmt === false) {
    http_response_code(500);
    echo json_encode(array("error" => "Failed to prepare statement: " . $conn->error));
    exit();
}

$stmt->bind_param("s", $tanggal);
$stmt->execute();
$result = $stmt->get_result();

$presensi_log = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Format times for display
        $row['jam_masuk'] = $row['jam_masuk'] ? date("H:i:s", strtotime($row['jam_masuk'])) : '-';
        $row['jam_keluar'] = $row['jam_keluar'] ? date("H:i:s", strtotime($row['jam_keluar'])) : '-';
        $presensi_log[] = $row;
    }
}

echo json_encode($presensi_log);

$stmt->close();
$conn->close();
?>
