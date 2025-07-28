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
    $id_izin = $data->id_izin;
    $action = $data->action; // 'setujui' atau 'tolak'
    
    // Ambil ID admin yang sedang login dari sesi (jika ada)
    // Asumsi: $_SESSION['admin_id'] menyimpan ID admin dari login_admin.php
    $admin_id = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : null; 
    $catatan_admin = null; // Anda bisa menambahkan input untuk catatan admin jika diperlukan

    $conn->begin_transaction();

    try {
        $new_status_izin = '';
        if ($action === 'setujui') {
            $new_status_izin = 'Disetujui';
        } elseif ($action === 'tolak') {
            $new_status_izin = 'Ditolak';
        } else {
            throw new Exception("Aksi tidak valid.");
        }

        // Update status di tabel 'izin'
        $stmt_update_izin = $conn->prepare("UPDATE izin SET status = ?, disetujui_oleh_admin_id = ?, tanggal_persetujuan = NOW(), catatan_admin = ? WHERE id_izin = ?");
        if (!$stmt_update_izin) {
            throw new Exception("Prepare statement (update izin status) failed: " . $conn->error);
        }
        // Perhatikan: 'siii' -> string, integer, integer, integer. Sesuaikan jika admin_id bukan integer.
        // Jika catatan_admin bisa null, gunakan 'sisi'
        $stmt_update_izin->bind_param("sisi", $new_status_izin, $admin_id, $catatan_admin, $id_izin);
        
        if (!$stmt_update_izin->execute()) {
            throw new Exception("Execute statement (update izin status) failed: " . $stmt_update_izin->error);
        }
        $stmt_update_izin->close();

        // Commit transaksi jika semua berhasil
        $conn->commit();
        echo json_encode(['status' => 'success', 'pesan' => 'Izin berhasil ' . $action . '.']);

    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['status' => 'error', 'pesan' => 'Gagal memproses izin: ' . $e->getMessage()]);
    } finally {
        if ($conn) {
            $conn->close();
        }
    }

} else {
    echo json_encode(['status' => 'error', 'pesan' => 'Metode request tidak valid atau data tidak lengkap.']);
}
?>
