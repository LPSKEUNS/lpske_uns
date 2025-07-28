<?php
session_start(); // Penting untuk mengelola sesi
header("Access-Control-Allow-Origin: *"); // Izinkan akses dari semua domain (untuk pengembangan)
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE"); // Tentukan metode yang diizinkan
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With"); // Tentukan header yang diizinkan
header('Content-Type: application/json; charset=UTF-8');

include 'koneksi.php'; // Pastikan path ke koneksi.php benar

$response = array(); // Inisialisasi array respons

// Periksa apakah koneksi database tersedia
if (!isset($conn)) {
    $response['status'] = 'error';
    $response['pesan'] = "Koneksi database tidak tersedia. Pastikan \$conn didefinisikan di koneksi.php.";
    echo json_encode($response);
    exit();
}

// Pastikan metode request adalah POST (sesuai dengan fetch di frontend)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents("php://input"));

    // Pastikan 'id' dan 'tipe' ada dan 'tipe' tidak kosong
    if (isset($data->id) && !empty($data->tipe)) { 
        
        $id = intval($data->id); // Konversi ID menjadi integer
        $tipe = $data->tipe;

        $tableName = '';
        $idColumnName = '';

        switch ($tipe) {
            case 'dosen':
                $tableName = 'dosen';
                $idColumnName = 'id_dosen';
                break;
            case 'asisten':
                $tableName = 'asisten';
                $idColumnName = 'id_asisten';
                break;
            case 'inventory':
                $tableName = 'inventory';
                $idColumnName = 'id_alat';
                break;
            default:
                // Jika tipe tidak dikenali atau tidak valid
                $response['status'] = 'error';
                $response['pesan'] = 'Tipe data **' . htmlspecialchars($tipe) . '** tidak dikenali atau tidak valid.'; 
                echo json_encode($response);
                exit;
        }

        // Siapkan statement SQL secara dinamis dan aman
        $sql = "DELETE FROM `$tableName` WHERE `$idColumnName` = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("i", $id); // "i" untuk integer
            
            if ($stmt->execute()) {
                // Periksa apakah ada baris yang terpengaruh (dihapus)
                if ($stmt->affected_rows > 0) {
                    $response['status'] = 'success';
                    $response['pesan'] = 'Data ' . htmlspecialchars($tipe) . ' berhasil dihapus.';
                } else {
                    $response['status'] = 'error';
                    $response['pesan'] = 'Data ' . htmlspecialchars($tipe) . ' dengan ID tersebut tidak ditemukan atau sudah dihapus.';
                }
            } else {
                $response['status'] = 'error';
                $response['pesan'] = 'Gagal mengeksekusi penghapusan data: ' . $stmt->error;
            }
            $stmt->close();
        } else {
            $response['status'] = 'error';
            $response['pesan'] = 'Gagal menyiapkan statement SQL: ' . $conn->error;
        }

    } else {
        $response['status'] = 'error';
        $response['pesan'] = 'Data ID atau Tipe tidak lengkap dalam request.';
    }
} else {
    $response['status'] = 'error';
    $response['pesan'] = 'Metode request tidak diizinkan.';
}

$conn->close();
echo json_encode($response);
?>