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
    <title>Kelola Data Inventaris</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 font-sans">

    <div class="container mx-auto p-8">
        <a href="dashboard.php" class="text-blue-600 hover:text-blue-800 transition duration-300 ease-in-out text-lg flex items-center mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
            </svg>
            Kembali Ke Dashboard
        </a>

        <h1 class="text-4xl font-extrabold text-gray-800 mb-8">Manajemen Data Inventaris LPSKE</h1>
        
        <div class="bg-white p-8 rounded-lg shadow-xl mb-8">
            <h2 class="text-3xl font-bold text-gray-700 mb-6 border-b pb-4">Tambah Data Inventaris</h2>
            <form id="formTambahInventaris" class="space-y-6">
                <div>
                    <label for="nama_alat" class="block text-sm font-semibold text-gray-700 mb-1">Nama Alat</label>
                    <input type="text" name="nama_alat" id="nama_alat"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-base" required>
                </div>
                <div>
                    <label for="kode_alat" class="block text-sm font-semibold text-gray-700 mb-1">Kode Alat</label>
                    <input type="text" name="kode_alat" id="kode_alat"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-base">
                </div>
                <div>
                    <label for="jumlah" class="block text-sm font-semibold text-gray-700 mb-1">Jumlah Total Unit</label>
                    <input type="number" name="jumlah" id="jumlah" min="0"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-base" required>
                </div>
                <div>
                    <label for="tersedia" class="block text-sm font-semibold text-gray-700 mb-1">Jumlah Tersedia </label>
                    <input type="number" name="tersedia" id="tersedia" min="0"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-base" required>
                </div>

                <!-- START: Tambahan untuk Keterangan -->
                <div>
                    <label for="keterangan" class="block text-sm font-semibold text-gray-700 mb-1">Keterangan (Contoh: 2 baik, 1 rusak)</label>
                    <textarea name="keterangan" id="keterangan" rows="3"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-base"></textarea>
                </div>
                <div>
                    <label for="status" class="block text-sm font-semibold text-gray-700 mb-1">Status</label>
                    <select name="status" id="status"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-base">
                        <option value="Tersedia">Tersedia</option>
                        <option value="Dipinjam">Dipinjam</option>
                        <option value="Rusak">Rusak</option>
                        <option value="Perbaikan">Tidak Bisa dicek</option>
                    </select>
                </div>
                <div class="mt-8">
                    <button type="submit" class="inline-flex justify-center py-3 px-6 border border-transparent shadow-sm text-lg font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-300 ease-in-out">
                        Simpan Inventaris
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-white p-8 rounded-lg shadow-xl mt-8">
            <h2 class="text-3xl font-bold text-gray-700 mb-6 border-b pb-4">Daftar Inventaris LPSKE</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-300">
                    <thead>
                        <tr>
                            <th class="py-3 px-6 bg-gray-200 text-gray-700 border-b border-gray-300 text-center text-sm font-semibold uppercase tracking-wider">Nama Alat</th>
                            <th class="py-3 px-6 bg-gray-200 text-gray-700 border-b border-gray-300 text-center text-sm font-semibold uppercase tracking-wider">Kode Alat</th>
                            <th class="py-3 px-6 bg-gray-200 text-gray-700 border-b border-gray-300 text-center text-sm font-semibold uppercase tracking-wider">Jumlah</th>
                            <th class="py-3 px-6 bg-gray-200 text-gray-700 border-b border-gray-300 text-center text-sm font-semibold uppercase tracking-wider">Tersedia</th>
                            <th class="py-3 px-6 bg-gray-200 text-gray-700 border-b border-gray-300 text-center text-sm font-semibold uppercase tracking-wider">Keterangan</th>
                            <th class="py-3 px-6 bg-gray-200 text-gray-700 border-b border-gray-300 text-center text-sm font-semibold uppercase tracking-wider">Status</th>
                            <th class="py-3 px-6 bg-gray-200 text-gray-700 border-b border-gray-300 text-center text-sm font-semibold uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        <?php
                        if (isset($conn)) {
                            $sql = "SELECT id_alat, nama_alat, kode_alat, jumlah, tersedia, keterangan, status FROM inventory ORDER BY nama_alat ASC";
                            $result = $conn->query($sql);

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td class='py-3 px-6 border-b border-gray-300 text-center'>" . htmlspecialchars($row['nama_alat']) . "</td>";
                                    echo "<td class='py-3 px-6 border-b border-gray-300 text-center'>" . htmlspecialchars($row['kode_alat']) . "</td>";
                                    echo "<td class='py-3 px-6 border-b border-gray-300 text-center'>" . htmlspecialchars($row['jumlah']) . "</td>";
                                    echo "<td class='py-3 px-6 border-b border-gray-300 text-center'>" . htmlspecialchars($row['tersedia']) . "</td>";
                                    echo "<td class='py-3 px-6 border-b border-gray-300 text-center'>" . htmlspecialchars($row['keterangan'] ?: '-') . "</td>";
                                    echo "<td class='py-3 px-6 border-b border-gray-300 text-center'>" . htmlspecialchars($row['status']) . "</td>";
                                    
                                    // Tombol hapus dengan pemanggilan fungsi yang benar
                                    echo "<td class='py-3 px-6 border-b border-gray-300 text-center'>";
                                    // Perhatikan 'inventaris' sebagai tipe data
                                    echo "  <button onclick=\"hapusData(" . $row['id_alat'] . ", 'inventory')\" class='bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600 transition duration-300 text-sm'>";
                                    echo "      <i class='fas fa-trash-alt'></i> Hapus";
                                    echo "  </button>";
                                    echo "</td>";
                                    
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='7' class='py-3 px-6 text-center text-gray-600'>Belum ada data inventaris.</td></tr>";
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

    <!-- ======================================================= -->
    <!-- SCRIPT YANG HILANG - TAMBAHKAN BLOK INI SECARA LENGKAP -->
    <!-- ======================================================= -->
    <script>
    function hapusData(id, tipe) {
        if (confirm("Apakah Anda yakin ingin menghapus inventaris ini?")) {
                fetch('../api/hapus_data.php', { // Pastikan path ini benar relatif dari kelola_inventaris.php
                    method: 'POST', // Menggunakan POST seperti yang dikonfigurasi di PHP
                    headers: {
                        'Content-Type': 'application/json' // Penting untuk mengirim JSON
                    },
                    body: JSON.stringify({ 
                        id: id,
                        tipe: tipe // KUNCI: Pastikan ini ADA dan BERNILAI 'inventaris'
                    })
                })
                .then(response => {
                    // Cek jika respons bukan JSON (misal error PHP)
                    const contentType = response.headers.get("content-type");
                    if (contentType && contentType.indexOf("application/json") !== -1) {
                        return response.json();
                    } else {
                        // Jika bukan JSON, baca sebagai teks dan lempar error
                        return response.text().then(text => { throw new Error("Respons bukan JSON: " + text); });
                    }
                })
                .then(data => {
                    if (data.status === 'success') {
                        alert(data.pesan);
                        location.reload(); // Muat ulang halaman untuk melihat perubahan
                    } else {
                        alert("Gagal menghapus: " + data.pesan);
                        console.error("Error dari server:", data.pesan); // Log error untuk debugging
                    }
                })
                .catch(error => {
                    console.error('Terjadi kesalahan saat fetch:', error);
                    alert('Terjadi kesalahan saat menghapus data. Periksa konsol browser.');
                });
            }
        }
  
       // ======================================================
    // Event listener untuk form tambah inventaris (AJAX Submission)
    // ======================================================
    document.addEventListener('DOMContentLoaded', function() {
        // ... (kode yang sudah ada untuk jumlahInput dan tersediaInput, jika ada) ...

        const formTambahInventaris = document.getElementById('formTambahInventaris'); // Ambil form berdasarkan ID

        if (formTambahInventaris) { // Pastikan form ditemukan
            formTambahInventaris.addEventListener('submit', function(e) {
                e.preventDefault(); // Mencegah form disubmit secara tradisional

                const formData = new FormData(this); // Ambil data dari form
                const data = {};
                formData.forEach((value, key) => (data[key] = value)); // Ubah FormData menjadi objek JavaScript

                // Client-side validation: Pastikan jumlah dan tersedia adalah angka valid
                if (isNaN(data.jumlah) || parseInt(data.jumlah) < 0 || isNaN(data.tersedia) || parseInt(data.tersedia) < 0) {
                    alert("Jumlah Total dan Jumlah Tersedia harus angka positif.");
                    return; 
                }
                if (parseInt(data.tersedia) > parseInt(data.jumlah)) {
                    alert("Jumlah Tersedia tidak boleh melebihi Jumlah Total.");
                    return;
                }

                // Kirim data menggunakan fetch API
                fetch('proses_tambah_inventaris.php', { // Path relatif ke proses_tambah_inventaris.php
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json' // Penting: Mengirim data sebagai JSON
                    },
                    body: JSON.stringify(data) // Mengubah objek JavaScript menjadi string JSON
                })
                .then(response => {
                     const contentType = response.headers.get("content-type");
                     if (contentType && contentType.includes("application/json")) {
                         return response.json(); // Jika respons JSON, parse sebagai JSON
                     } else {
                         return response.text().then(text => { throw new Error("Respons bukan JSON: " + text); }); // Jika bukan, lempar error
                     }
                })
                .then(result => {
                    alert(result.pesan); // Tampilkan pesan dari server
                    if (result.status === 'success') {
                        this.reset(); // Kosongkan form setelah sukses
                        // Pastikan loadAdminInventory() ada dan dipanggil untuk refresh tabel
                        if (typeof loadAdminInventory === 'function') {
                            loadAdminInventory(); 
                        } else {
                            location.reload(); // Fallback: reload penuh halaman jika fungsi tidak ditemukan
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error); // Log error ke konsol browser
                    alert('Terjadi kesalahan saat menambahkan data. Periksa konsol browser.');
                });
            });
        }

        // Pastikan loadAdminInventory dipanggil saat DOM selesai dimuat
        loadAdminInventory(); 
    });
    </script>

</body>
</html>
