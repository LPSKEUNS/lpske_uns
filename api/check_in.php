<?php
session_start();

header("Access-Control-Allow-Origin: http://localhost");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

include 'koneksi.php';

// Validasi session
if (!isset($_SESSION['id_asisten'])) {
    echo json_encode(["status" => "error", "pesan" => "Anda belum login."]);
    exit;
}

$id_asisten = $_SESSION['id_asisten'];
$nama_asisten = $_SESSION['nama_asisten']; // Opsional, hanya jika perlu

$tanggal = date("Y-m-d");

// Cek apakah sudah check-in hari ini
$sql_check = "SELECT * FROM presensi WHERE id_asisten = ? AND tanggal = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("is", $id_asisten, $tanggal);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows > 0) {
    $existing_entry = $result_check->fetch_assoc();
    if (!empty($existing_entry['jam_masuk']) && empty($existing_entry['jam_keluar'])) {
        echo json_encode(["status" => "error", "pesan" => "Anda sudah Check-In hari ini."]);
    } else {
        // Update jam_masuk baru jika sudah check-out sebelumnya
        $sql = "UPDATE presensi SET jam_masuk = NOW(), jam_keluar = NULL, status = 'Check-In' WHERE id_asisten = ? AND tanggal = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $id_asisten, $tanggal);
        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "pesan" => "Check-In berhasil!"]);
        } else {
            echo json_encode(["status" => "error", "pesan" => "Gagal Check-In: " . $stmt->error]);
        }
    }
} else {
    // Insert record baru
    $sql = "INSERT INTO presensi (id_asisten, tanggal, jam_masuk, status) VALUES (?, ?, NOW(), 'Check-In')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $id_asisten, $tanggal);
    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "pesan" => "Check-In berhasil!"]);
    } else {
        echo json_encode(["status" => "error", "pesan" => "Gagal Check-In: " . $stmt->error]);
    }
}

$stmt_check->close();
$conn->close();
?>
