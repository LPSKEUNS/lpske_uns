<?php
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
    // KOREKSI: Tambahkan kolom 'keterangan' ke dalam SELECT statement
    $sql = "SELECT id_alat, nama_alat, kode_alat, jumlah, tersedia, keterangan, status FROM inventory ORDER BY nama_alat ASC";

    $result = $conn->query($sql);

    $inventory_data = [];
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $inventory_data[] = [
                'id_alat'   => $row['id_alat'],
                'nama_alat' => $row['nama_alat'],
                'kode_alat' => $row['kode_alat'],
                'jumlah'    => $row['jumlah'],
                'tersedia'  => $row['tersedia'],
                'keterangan'=> $row['keterangan'], // <<< Tambahkan ini
                'status'    => $row['status']
            ];
        }
    }
    
    echo json_encode($inventory_data);

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'pesan' => 'Gagal mengambil data inventaris: ' . $e->getMessage()]);
} finally {
    if ($conn) {
        $conn->close();
    }
}
?>