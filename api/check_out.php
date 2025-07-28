<?php
session_start(); // WAJIB ADA

header("Access-Control-Allow-Origin: http://localhost"); // sesuaikan kalau beda host
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

include 'koneksi.php';

// Pastikan session tersedia
if (!isset($_SESSION['id_asisten'])) {
    echo json_encode(["status" => "error", "pesan" => "Anda belum login."]);
    exit;
}

$id_asisten = $_SESSION['id_asisten'];
$tanggal = date("Y-m-d");

// Cek apakah sudah Check-In hari ini dan belum Check-Out
$sql_check = "SELECT * FROM presensi WHERE id_asisten = ? AND tanggal = ? AND jam_masuk IS NOT NULL AND jam_keluar IS NULL";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("is", $id_asisten, $tanggal);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows > 0) {
    $sql = "UPDATE presensi SET jam_keluar = NOW(), status = 'Check-Out' WHERE id_asisten = ? AND tanggal = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $id_asisten, $tanggal);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "pesan" => "Check-Out berhasil!"]);
    } else {
        echo json_encode(["status" => "error", "pesan" => "Gagal Check-Out: " . $stmt->error]);
    }
} else {
    echo json_encode(["status" => "error", "pesan" => "Anda belum Check-In atau sudah Check-Out hari ini."]);
}

$stmt_check->close();
$conn->close();
?>
