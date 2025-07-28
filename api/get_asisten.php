<?php
// Pastikan header CORS di sini juga!
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
include 'koneksi.php';

// Di dalam get_asisten.php
$sql = "SELECT nama, nim, angkatan FROM asisten ORDER BY angkatan DESC";
$result = $conn->query($sql);

$asisten = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $asisten[] = $row;
    }
}

echo json_encode($asisten);

$conn->close();
?>