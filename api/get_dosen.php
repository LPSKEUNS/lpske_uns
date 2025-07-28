<?php
// Pastikan header CORS di sini juga!
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
include 'koneksi.php';

$sql = "SELECT id_dosen, nama_lengkap, jabatan, nip FROM dosen";
$result = $conn->query($sql);

$dosen = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $dosen[] = $row;
    }
}

echo json_encode($dosen);

$conn->close();
?>