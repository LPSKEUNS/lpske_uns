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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Persetujuan Izin Penelitian</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 font-sans">

    <div class="container mx-auto p-8">
        <a href="dashboard.php" class="text-blue-600 hover:text-blue-800 transition duration-300 ease-in-out text-lg flex items-center mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
            </svg>
            Kembali Ke Dashboard
        </a>

        <h1 class="text-4xl font-extrabold text-gray-800 mb-8">Persetujuan Izin Penelitian/Kegiatan</h1>

        <?php
        // Menampilkan pesan sukses atau error (jika ada)
        if (isset($_SESSION['success_message'])) {
            echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">';
            echo '<strong class="font-bold">Sukses!</strong>';
            echo '<span class="block sm:inline ml-2">' . $_SESSION['success_message'] . '</span>';
            echo '<span class="absolute top-0 bottom-0 right-0 px-4 py-3 cursor-pointer" onclick="this.parentNode.style.display=\'none\';">';
            echo '<svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.414l-2.651 2.651a1.2 1.2 0 1 1-1.697-1.697L8.303 9.707l-2.651-2.651a1.2 1.2 0 0 1 1.697-1.697L10 8.303l2.651-2.651a1.2 1.2 0 0 1 1.697 1.697L11.697 10l2.651 2.651a1.2 1.2 0 0 1 0 1.698z"/></svg>';
            echo '</span>';
            echo '</div>';
            unset($_SESSION['success_message']); // Hapus pesan setelah ditampilkan
        }
        if (isset($_SESSION['error_message'])) {
            echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">';
            echo '<strong class="font-bold">Error!</strong>';
            echo '<span class="block sm:inline ml-2">' . $_SESSION['error_message'] . '</span>';
            echo '<span class="absolute top-0 bottom-0 right-0 px-4 py-3 cursor-pointer" onclick="this.parentNode.style.display=\'none\';">';
            echo '<svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.414l-2.651 2.651a1.2 1.2 0 1 1-1.697-1.697L8.303 9.707l-2.651-2.651a1.2 1.2 0 0 1 1.697-1.697L10 8.303l2.651-2.651a1.2 1.2 0 0 1 1.697 1.697L11.697 10l2.651 2.651a1.2 1.2 0 0 1 0 1.698z"/></svg>';
            echo '</span>';
            echo '</div>';
            unset($_SESSION['error_message']); // Hapus pesan setelah ditampilkan
        }
        ?>

        <div class="bg-white p-8 rounded-lg shadow-xl mt-8">
            <h2 class="text-3xl font-bold text-gray-700 mb-6 border-b pb-4">Daftar Izin Menunggu Persetujuan</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-300">
                    <thead>
                        <tr>
                            <th class="py-3 px-6 bg-gray-200 text-gray-700 border-b border-gray-300 text-center text-sm font-semibold uppercase tracking-wider">ID Izin</th>
                            <th class="py-3 px-6 bg-gray-200 text-gray-700 border-b border-gray-300 text-center text-sm font-semibold uppercase tracking-wider">Judul Penelitian</th>
                            <th class="py-3 px-6 bg-gray-200 text-gray-700 border-b border-gray-300 text-center text-sm font-semibold uppercase tracking-wider">Deskripsi</th>
                            <th class="py-3 px-6 bg-gray-200 text-gray-700 border-b border-gray-300 text-center text-sm font-semibold uppercase tracking-wider">Pengaju</th>
                            <th class="py-3 px-6 bg-gray-200 text-gray-700 border-b border-gray-300 text-center text-sm font-semibold uppercase tracking-wider">Tgl. Pengajuan</th>
                            <th class="py-3 px-6 bg-gray-200 text-gray-700 border-b border-gray-300 text-center text-sm font-semibold uppercase tracking-wider">Status</th>
                            <th class="py-3 px-6 bg-gray-200 text-gray-700 border-b border-gray-300 text-center text-sm font-semibold uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="izin-approval-table" class="text-gray-700">
                        <?php
                        if (isset($conn)) {
                            // Query untuk mengambil izin dengan status 'Diajukan'
                            $sql = "SELECT id_izin, judul_penelitian, deskripsi, nama_pengaju, tanggal_pengajuan, status 
                                    FROM izin 
                                    WHERE status = 'Diajukan'
                                    ORDER BY tanggal_pengajuan ASC";
                            $result = $conn->query($sql);

                            if ($result && $result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td class='py-3 px-6 border-b border-gray-300 text-center'>" . htmlspecialchars($row['id_izin']) . "</td>";
                                    echo "<td class='py-3 px-6 border-b border-gray-300 text-center'>" . htmlspecialchars($row['judul_penelitian']) . "</td>";
                                    echo "<td class='py-3 px-6 border-b border-gray-300 text-center'>" . htmlspecialchars($row['deskripsi']) . "</td>";
                                    echo "<td class='py-3 px-6 border-b border-gray-300 text-center'>" . htmlspecialchars($row['nama_pengaju']) . "</td>";
                                    echo "<td class='py-3 px-6 border-b border-gray-300 text-center'>" . htmlspecialchars($row['tanggal_pengajuan']) . "</td>";
                                    echo "<td class='py-3 px-6 border-b border-gray-300 text-center'>" . htmlspecialchars($row['status']) . "</td>";
                                    echo "<td class='py-3 px-6 border-b border-gray-300 text-center'>";
                                    echo "  <button onclick=\"processIzin(" . $row['id_izin'] . ", 'setujui')\" class='px-3 py-1 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50 mr-2'>Setujui</button>";
                                    echo "  <button onclick=\"processIzin(" . $row['id_izin'] . ", 'tolak')\" class='px-3 py-1 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50'>Tolak</button>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='7' class='py-3 px-6 text-center text-gray-600'>Tidak ada pengajuan izin menunggu persetujuan.</td></tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7' class='py-3 px-6 text-center text-red-600'>Error: Koneksi database tidak tersedia.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Custom Modal for messages -->
    <div id="custom-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white p-6 rounded-lg shadow-xl max-w-sm w-full mx-auto">
            <h3 id="modal-title" class="text-xl font-bold mb-4"></h3>
            <p id="modal-body" class="text-gray-700 mb-6"></p>
            <div class="flex justify-end">
                <button id="modal-close-btn" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Tutup</button>
            </div>
        </div>
    </div>

    <script>
        const API_BASE_URL = '../api/'; // Sesuaikan path ke folder API Anda

        // Fungsi untuk menampilkan modal
        function showModal(title, body) {
            document.getElementById('modal-title').textContent = title;
            document.getElementById('modal-body').textContent = body;
            document.getElementById('custom-modal').classList.remove('hidden');
        }

        // Fungsi untuk menutup modal
        document.getElementById('modal-close-btn').addEventListener('click', function() {
            document.getElementById('custom-modal').classList.add('hidden');
        });

        // Fungsi untuk memproses persetujuan/penolakan izin
        function processIzin(id_izin, action) {
            const confirmMessage = action === 'setujui' ? 
                "Apakah Anda yakin ingin MENYETUJUI izin ini?" : 
                "Apakah Anda yakin ingin MENOLAK izin ini?";
            
            if (!confirm(confirmMessage)) {
                return; // Batalkan jika user tidak yakin
            }

            fetch(API_BASE_URL + 'proses_persetujuan_izin.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ id_izin: id_izin, action: action })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    showModal('Sukses', data.pesan);
                    // Refresh halaman atau tabel setelah berhasil
                    location.reload(); 
                } else {
                    showModal('Gagal', data.pesan);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showModal('Error', 'Terjadi kesalahan saat memproses izin.');
            });
        }
    </script>
</body>

</html>
