<?php
// Gatekeeper & koneksi database
session_start();
if (!isset($_SESSION['admin_loggedin'])) {
    header('Location: ../login_admin.php');
    exit;
}
include '../api/koneksi.php'; // Sesuaikan dengan nama file koneksi Anda
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kelola Data Dosen</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Link untuk ikon Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-100">

    <div class="container mx-auto p-8">
        <a href="dashboard.php" class="text-blue-500 hover:text-blue-700">&larr; Kembali ke Dashboard</a>
        <h1 class="text-3xl font-bold my-4">Manajemen Data Dosen</h1>

        <div class="bg-white p-6 rounded-lg shadow-lg mb-8">
            <h2 class="text-2xl font-semibold mb-4">Tambah Dosen Baru</h2>
            <form action="proses_tambah_dosen.php" method="POST" class="space-y-6">
                <div>
                    <label for="nama_lengkap" class="block text-sm font-semibold text-gray-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" id="nama_lengkap"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-base" required>
                </div>
                <div>
                    <label for="jabatan" class="block text-sm font-semibold text-gray-700 mb-1">Jabatan</label>
                    <input type="text" name="jabatan" id="jabatan"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-base">
                </div>
                <div>
                    <label for="nip" class="block text-sm font-semibold text-gray-700 mb-1">NIP</label>
                    <input type="text" name="nip" id="nip"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-base" required>
                </div>
                <div class="mt-8">
                    <button type="submit" class="inline-flex justify-center py-3 px-6 border border-transparent shadow-sm text-lg font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-300 ease-in-out">
                        Simpan Dosen
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h2 class="text-2xl font-semibold mb-4">Daftar Dosen LPSKE</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-300">
                    <thead>
                        <tr>
                            <th class="py-3 px-6 bg-gray-200 text-gray-700 border-b border-gray-300 text-center text-sm font-semibold uppercase tracking-wider">Nama Lengkap</th>
                            <th class="py-3 px-6 bg-gray-200 text-gray-700 border-b border-gray-300 text-center text-sm font-semibold uppercase tracking-wider">Jabatan</th>
                            <th class="py-3 px-6 bg-gray-200 text-gray-700 border-b border-gray-300 text-center text-sm font-semibold uppercase tracking-wider">NIP</th>
                            <!-- Header Kolom Aksi -->
                            <th class="py-3 px-6 bg-gray-200 text-gray-700 border-b border-gray-300 text-center text-sm font-semibold uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        <?php
                        $sql = "SELECT id_dosen, nama_lengkap, jabatan, nip FROM dosen ORDER BY nama_lengkap ASC";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td class='py-3 px-6 border-b border-gray-300 text-center'>" . htmlspecialchars($row['nama_lengkap']) . "</td>";
                                echo "<td class='py-3 px-6 border-b border-gray-300 text-center'>" . htmlspecialchars($row['jabatan']) . "</td>";
                                echo "<td class='py-3 px-6 border-b border-gray-300 text-center'>" . htmlspecialchars($row['nip']) . "</td>";
                                
                                // Tombol Hapus dengan pemanggilan fungsi JavaScript yang benar
                                echo "<td class='py-3 px-6 border-b border-gray-300 text-center'>";
                                echo "  <button onclick=\"hapusData(" . $row['id_dosen'] . ", 'dosen')\" class='bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600 transition duration-300 text-sm'>";
                                echo "      <i class='fas fa-trash-alt'></i> Hapus";
                                echo "  </button>";
                                echo "</td>";
                                
                                echo "</tr>";
                            }
                        } else {
                            // Ubah colspan menjadi 4 karena ada tambahan 1 kolom
                            echo "<tr><td colspan='4' class='py-3 px-6 text-center text-gray-600'>Belum ada data asisten.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Script JavaScript Universal untuk Fungsi Hapus -->
    <script>
    function hapusData(id, tipe) {
        const namaTipe = tipe.charAt(0).toUpperCase() + tipe.slice(1);
        if (!confirm(`Apakah Anda yakin ingin menghapus data ${namaTipe} ini? Tindakan ini tidak dapat dibatalkan.`)) {
            return;
        }

        // Semua proses hapus menunjuk ke satu file API universal
        const apiUrl = '../api/hapus_data.php';

        fetch(apiUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: id, tipe: tipe })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert(data.pesan);
                location.reload(); // Muat ulang halaman untuk melihat perubahan
            } else {
                alert('Gagal menghapus data: ' + data.pesan);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan pada sistem.');
        });
    }
    </script>

</body>
</html>
