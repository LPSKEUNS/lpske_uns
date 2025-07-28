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
    $id_pinjam = $data->id_pinjam;
    $jumlah_kembali = 1; // Asumsi: jumlah yang dikembalikan selalu 1 untuk setiap transaksi pengembalian

    // Validasi input tambahan
    if ($jumlah_kembali <= 0) {
        echo json_encode(['status' => 'error', 'pesan' => 'Jumlah pengembalian harus lebih dari 0.']);
        exit();
    }

    $conn->begin_transaction();

    try {
        // 1. Ambil id_alat dan status peminjaman dari tabel peminjaman
        $stmt_get_pinjam_info = $conn->prepare("SELECT id_alat, status FROM peminjaman WHERE id_pinjam = ? FOR UPDATE");
        if (!$stmt_get_pinjam_info) {
            throw new Exception("Prepare statement (get pinjam info) failed: " . $conn->error);
        }
        $stmt_get_pinjam_info->bind_param("i", $id_pinjam);
        $stmt_get_pinjam_info->execute();
        $result_pinjam_info = $stmt_get_pinjam_info->get_result();
        $pinjam_info = $result_pinjam_info->fetch_assoc();
        $stmt_get_pinjam_info->close();

        if (!$pinjam_info) {
            throw new Exception("Data peminjaman tidak ditemukan.");
        }
        $id_alat = $pinjam_info['id_alat'];
        $current_peminjaman_status = $pinjam_info['status'];

        // Hanya izinkan pengembalian jika status peminjaman adalah 'Dipinjam'
        if ($current_peminjaman_status !== 'Dipinjam') {
            throw new Exception("Peminjaman ini tidak dalam status 'Dipinjam' sehingga tidak dapat dikembalikan.");
        }

        // 2. Update status peminjaman menjadi 'Selesai' di tabel 'peminjaman'
        $stmt_update_pinjam = $conn->prepare("UPDATE peminjaman SET status = 'Selesai', tanggal_kembali = NOW() WHERE id_pinjam = ?");
        if (!$stmt_update_pinjam) {
            throw new Exception("Prepare statement (update peminjaman status) failed: " . $conn->error);
        }
        $stmt_update_pinjam->bind_param("i", $id_pinjam);
        if (!$stmt_update_pinjam->execute()) {
            throw new Exception("Execute statement (update peminjaman status) failed: " . $stmt_update_pinjam->error);
        }
        $stmt_update_pinjam->close();

        // 3. Ambil jumlah 'tersedia' saat ini, 'jumlah' total, dan 'status' dari tabel 'inventory'
        $stmt_get_inventory = $conn->prepare("SELECT jumlah, tersedia, status FROM inventory WHERE id_alat = ? FOR UPDATE");
        if (!$stmt_get_inventory) {
            throw new Exception("Prepare statement (get inventory) failed: " . $conn->error);
        }
        $stmt_get_inventory->bind_param("i", $id_alat);
        $stmt_get_inventory->execute();
        $result_inventory = $stmt_get_inventory->get_result();
        $item_inventory = $result_inventory->fetch_assoc();
        $stmt_get_inventory->close();

        if (!$item_inventory) {
            throw new Exception("Alat terkait tidak ditemukan di inventaris.");
        }

        $current_tersedia_inventory = $item_inventory['tersedia'];
        $total_jumlah_inventory = $item_inventory['jumlah'];
        $current_status_inventory = $item_inventory['status'];

        // 4. Tambahkan jumlah 'tersedia' di tabel 'inventory'
        $new_tersedia_inventory = $current_tersedia_inventory + $jumlah_kembali;

        // Pastikan 'tersedia' tidak melebihi 'jumlah' total
        if ($new_tersedia_inventory > $total_jumlah_inventory) {
            $new_tersedia_inventory = $total_jumlah_inventory;
        }

        $new_status_inventory_to_set = $current_status_inventory; // Default: pertahankan status saat ini

        // KOREKSI: Logika untuk mengubah status inventaris menjadi 'Tersedia'
        // Jika tidak ada lagi peminjaman aktif untuk alat ini
        // DAN jumlah yang tersedia sekarang sama dengan total jumlah alat (semua sudah kembali),
        // DAN status saat ini BUKAN 'Rusak' atau 'Tidak Bisa dicek',
        // maka baru ubah status inventaris menjadi 'Tersedia'.
        $stmt_check_active_loans = $conn->prepare("SELECT COUNT(*) AS active_loans FROM peminjaman WHERE id_alat = ? AND status = 'Dipinjam'");
        if (!$stmt_check_active_loans) {
            throw new Exception("Prepare statement (check active loans) failed: " . $conn->error);
        }
        $stmt_check_active_loans->bind_param("i", $id_alat);
        $stmt_check_active_loans->execute();
        $result_active_loans = $stmt_check_active_loans->get_result();
        $active_loans_info = $result_active_loans->fetch_assoc();
        $active_loans_count = $active_loans_info['active_loans'];
        $stmt_check_active_loans->close();

        if ($active_loans_count === 0 && $new_tersedia_inventory === $total_jumlah_inventory && 
            $current_status_inventory !== 'Rusak' && $current_status_inventory !== 'Tidak Bisa dicek') {
            $new_status_inventory_to_set = 'Tersedia';
        } 
        // Else if the inventory item was "Dipinjam" and there are still active loans, keep it "Dipinjam"
        else if ($current_status_inventory === 'Dipinjam' && $active_loans_count > 0) {
            $new_status_inventory_to_set = 'Dipinjam';
        }
        // For other statuses like 'Rusak' or 'Tidak Bisa dicek', the status should remain unchanged, 
        // which is already handled by $new_status_inventory_to_set = $current_status_inventory;

        $stmt_update_inventory = $conn->prepare("UPDATE inventory SET tersedia = ?, status = ? WHERE id_alat = ?");
        if (!$stmt_update_inventory) {
            throw new Exception("Prepare statement (update inventory) failed: " . $conn->error);
        }
        $stmt_update_inventory->bind_param("isi", $new_tersedia_inventory, $new_status_inventory_to_set, $id_alat);
        if (!$stmt_update_inventory->execute()) {
            throw new Exception("Execute statement (update inventory) failed: " . $stmt_update_inventory->error);
        }
        $stmt_update_inventory->close();

        // Commit transaksi jika semua berhasil
        $conn->commit();
        echo json_encode(['status' => 'success', 'pesan' => 'Alat berhasil dikembalikan.']);

    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['status' => 'error', 'pesan' => 'Gagal mengembalikan alat: ' . $e->getMessage()]);
    } finally {
        if ($conn) {
            $conn->close();
        }
    }

} else {
    echo json_encode(['status' => 'error', 'pesan' => 'Metode request tidak valid atau data tidak lengkap.']);
}
?>