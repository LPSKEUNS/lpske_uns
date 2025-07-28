<?php
// proses_tambah_inventaris.php
session_start();
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header('Content-Type: application/json; charset=UTF-8');

include '../api/koneksi.php'; // SESUAIKAN DENGAN PATH FILE KONEKSI.PHP ANDA

$response = array();

// Pastikan koneksi database tersedia
if (!isset($conn)) {
    $response['status'] = 'error';
    $response['pesan'] = "Koneksi database tidak tersedia. Pastikan \$conn didefinisikan di koneksi.php.";
    echo json_encode($response);
    exit();
}

// Pastikan metode request adalah POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data JSON dari body request
    $data = json_decode(file_get_contents("php://input"), true); // `true` agar jadi array asosiatif

    // Validasi data yang diterima: pastikan semua field yang dibutuhkan ada
    if (
        isset($data['nama_alat']) &&
        isset($data['kode_alat']) &&
        isset($data['jumlah']) &&
        isset($data['tersedia']) &&
        isset($data['keterangan']) &&
        isset($data['status'])
    ) {
        // Sanitasi dan validasi input
        $nama_alat = $conn->real_escape_string($data['nama_alat']);
        $kode_alat = $conn->real_escape_string($data['kode_alat']);
        $jumlah = intval($data['jumlah']);
        $tersedia = intval($data['tersedia']);
        $keterangan = $conn->real_escape_string($data['keterangan']);
        $status = $conn->real_escape_string($data['status']);

        // Validasi tambahan: jumlah tersedia tidak boleh melebihi jumlah total
        if ($tersedia > $jumlah) {
            $response['status'] = 'error';
            $response['pesan'] = "Jumlah Tersedia tidak boleh melebihi Jumlah Total.";
            echo json_encode($response);
            exit();
        }

        // Query SQL untuk memasukkan data
        $sql = "INSERT INTO inventory (nama_alat, kode_alat, jumlah, tersedia, keterangan, status) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            // Bind parameter ke statement
            $stmt->bind_param("ssiiss", $nama_alat, $kode_alat, $jumlah, $tersedia, $keterangan, $status);

            // Eksekusi statement
            if ($stmt->execute()) {
                $response['status'] = 'success';
                $response['pesan'] = "Data inventaris '$nama_alat' berhasil ditambahkan.";
            } else {
                $response['status'] = 'error';
                $response['pesan'] = "Gagal menambahkan data inventaris: " . $stmt->error;
            }
            $stmt->close(); // Tutup statement
        } else {
            $response['status'] = 'error';
            $response['pesan'] = "Gagal menyiapkan statement SQL: " . $conn->error;
        }

    } else {
        $response['status'] = 'error';
        $response['pesan'] = "Data yang dikirim tidak lengkap. Pastikan semua field terisi.";
    }
} else {
    $response['status'] = 'error';
    $response['pesan'] = "Metode request tidak diizinkan.";
}

$conn->close(); // Tutup koneksi database
echo json_encode($response); // Kirim respons dalam format JSON
?>