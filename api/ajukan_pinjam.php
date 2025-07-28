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

$data = json_decode(file_get_contents("php://input"));

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $data) {
    $id_alat = $data->id_alat;
    $nama_peminjam = $data->nama_peminjam;
    $keperluan = $data->keperluan;
    // Asumsi: jumlah yang dipinjam selalu 1 per transaksi, atau bisa diambil dari $data->jumlah_pinjam
    $jumlah_pinjam = isset($data->jumlah_pinjam) ? (int)$data->jumlah_pinjam : 1; 

    // Validasi input tambahan
    if ($jumlah_pinjam <= 0) {
        echo json_encode(['status' => 'error', 'pesan' => 'Jumlah peminjaman harus lebih dari 0.']);
        exit();
    }

    $conn->begin_transaction();

    try {
        // Ambil data tersedia, jumlah, dan status dari inventory untuk id_alat yang relevan
        $stmt_check = $conn->prepare("SELECT jumlah, tersedia, status FROM inventory WHERE id_alat = ? FOR UPDATE");
        if (!$stmt_check) {
            throw new Exception("Prepare statement (check inventory) failed: " . $conn->error);
        }
        $stmt_check->bind_param("i", $id_alat);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        $item = $result_check->fetch_assoc();
        $stmt_check->close();

        if (!$item) {
            throw new Exception("Alat tidak ditemukan.");
        }

        // Validasi ketersediaan langsung dari kolom 'tersedia' di DB
        if ($item['tersedia'] < $jumlah_pinjam) {
            // KOREKSI: Pesan error lebih informatif, menampilkan sisa stok
            throw new Exception("Alat tidak cukup tersedia untuk dipinjam. Sisa: " . $item['tersedia'] . " unit."); 
        }

        // Hitung nilai 'tersedia' yang baru
        $new_tersedia_inventory = $item['tersedia'] - $jumlah_pinjam; 
        $new_status_inventory = $item['status']; // Pertahankan status awal

        // Jika tersedia menjadi 0 atau kurang, dan status saat ini 'Tersedia', ubah status menjadi 'Dipinjam'
        // KOREKSI: Gunakan perbandingan yang lebih ketat jika Anda yakin dengan nilai ENUM 'Tersedia'
        if ($new_tersedia_inventory <= 0 && $new_status_inventory === 'Tersedia') { 
            $new_status_inventory = 'Dipinjam';
        }
        // Jika statusnya bukan 'Tersedia' (misal 'Rusak', 'Tidak Bisa dicek'), jangan ubah menjadi 'Dipinjam'
        // Ini sudah ditangani oleh logika di atas yang hanya mengubah jika $new_status_inventory === 'Tersedia'

        $stmt_update_inventory = $conn->prepare("UPDATE inventory SET tersedia = ?, status = ? WHERE id_alat = ?");
        if (!$stmt_update_inventory) {
            throw new Exception("Prepare statement (update inventory) failed: " . $conn->error);
        }
        $stmt_update_inventory->bind_param("isi", $new_tersedia_inventory, $new_status_inventory, $id_alat);
        
        if (!$stmt_update_inventory->execute()) {
            throw new Exception("Execute statement (update inventory) failed: " . $stmt_update_inventory->error);
        }
        $stmt_update_inventory->close();

        // Catat peminjaman di tabel 'peminjaman'
        $stmt_insert_pinjam = $conn->prepare("INSERT INTO peminjaman (id_alat, nama_peminjam, keperluan, tanggal_pinjam, status) VALUES (?, ?, ?, NOW(), 'Dipinjam')");
        if (!$stmt_insert_pinjam) {
            throw new Exception("Prepare statement (insert peminjaman) failed: " . $conn->error);
        }
        $stmt_insert_pinjam->bind_param("iss", $id_alat, $nama_peminjam, $keperluan);
        if (!$stmt_insert_pinjam->execute()) {
            throw new Exception("Execute statement (insert peminjaman) failed: " . $stmt_insert_pinjam->error);
        }
        $stmt_insert_pinjam->close();

        $conn->commit();
        echo json_encode(['status' => 'success', 'pesan' => 'Peminjaman berhasil.']);

    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['status' => 'error', 'pesan' => 'Gagal mengajukan peminjaman: ' . $e->getMessage()]);
    } finally {
        if ($conn) {
            $conn->close();
        }
    }

} else {
    echo json_encode(['status' => 'error', 'pesan' => 'Metode request tidak valid atau data tidak lengkap.']);
}
?>