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
    <title>Kelola Data Presensi Asisten</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Link untuk ikon Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Optional: Custom styling for better table appearance */
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px 15px;
            border: 1px solid #e2e8f0; /* Tailwind's border-gray-300 */
            text-align: left;
        }
        th {
            background-color: #edf2f7; /* Tailwind's bg-gray-200 */
            color: #4a5568; /* Tailwind's text-gray-700 */
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.875rem; /* Tailwind's text-sm */
        }
        tbody tr:nth-child(even) {
            background-color: #f7fafc; /* Tailwind's bg-gray-50 */
        }
        tbody tr:hover {
            background-color: #e2e8f0; /* Tailwind's bg-gray-200 */
        }
    </style>
</head>

<body class="bg-gray-100 font-sans antialiased">

    <div class="container mx-auto p-8">
        <a href="dashboard.php" class="text-blue-600 hover:text-blue-800 flex items-center mb-6">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Dashboard
        </a>
        <h1 class="text-4xl font-extrabold text-gray-800 my-6">Manajemen Data Presensi Asisten</h1>

        <div class="bg-white p-6 rounded-xl shadow-lg mb-8">
            <h2 class="text-2xl font-semibold text-gray-700 mb-4">Filter Presensi Berdasarkan Tanggal</h2>
            <div class="flex items-center space-x-4">
                <label for="tanggal_filter" class="text-lg font-medium text-gray-600">Pilih Tanggal:</label>
                <input type="date" id="tanggal_filter"
                       class="mt-1 block w-auto border border-gray-300 rounded-md shadow-sm py-2 px-4 focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-base"
                       value="<?php echo date('Y-m-d'); ?>">
                <button id="btn_filter" class="bg-blue-600 text-white px-5 py-2 rounded-lg shadow-md hover:bg-blue-700 transition duration-300 ease-in-out flex items-center">
                    <i class="fas fa-filter mr-2"></i> Tampilkan
                </button>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-lg">
            <h2 class="text-2xl font-semibold text-gray-700 mb-4">Daftar Presensi Asisten</h2>
            <div id="loading_indicator" class="text-center text-blue-600 text-lg my-4 hidden">
                <i class="fas fa-spinner fa-spin mr-2"></i> Memuat data presensi...
            </div>
            <div id="error_message" class="text-center text-red-600 text-lg my-4 hidden">
                <i class="fas fa-exclamation-triangle mr-2"></i> Gagal memuat data presensi. Silakan coba lagi.
            </div>
            <div class="overflow-x-auto rounded-lg border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Presensi</th>
                            <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Asisten</th>
                            <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jam Masuk</th>
                            <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jam Keluar</th>
                            <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody id="presensi_table_body" class="bg-white divide-y divide-gray-200">
                        <!-- Data presensi akan dimuat di sini oleh JavaScript -->
                        <tr><td colspan="6" class="py-4 px-6 text-center text-gray-500">Pilih tanggal untuk melihat data presensi.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tanggalFilter = document.getElementById('tanggal_filter');
            const btnFilter = document.getElementById('btn_filter');
            const presensiTableBody = document.getElementById('presensi_table_body');
            const loadingIndicator = document.getElementById('loading_indicator');
            const errorMessage = document.getElementById('error_message');

            // Fungsi untuk memuat data presensi
            async function loadPresensiData(date) {
                loadingIndicator.classList.remove('hidden');
                errorMessage.classList.add('hidden');
                presensiTableBody.innerHTML = ''; // Kosongkan tabel sebelum memuat data baru

                try {
                    const response = await fetch(`../api/get_presensi_log.php?tanggal=${date}`);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    const data = await response.json();

                    if (data.length > 0) {
                        data.forEach(presensi => {
                            const row = presensiTableBody.insertRow();
                            row.classList.add('hover:bg-gray-100'); // Add hover effect
                            row.insertCell().textContent = presensi.id_presensi;
                            row.insertCell().textContent = presensi.nama_asisten;
                            row.insertCell().textContent = presensi.tanggal;
                            row.insertCell().textContent = presensi.jam_masuk;
                            row.insertCell().textContent = presensi.jam_keluar;
                            row.insertCell().textContent = presensi.status;
                        });
                    } else {
                        const row = presensiTableBody.insertRow();
                        const cell = row.insertCell();
                        cell.colSpan = 6;
                        cell.classList.add('py-4', 'px-6', 'text-center', 'text-gray-500');
                        cell.textContent = 'Tidak ada data presensi untuk tanggal ini.';
                    }
                } catch (error) {
                    console.error('Error fetching presensi data:', error);
                    errorMessage.classList.remove('hidden');
                    presensiTableBody.innerHTML = `<tr><td colspan="6" class="py-4 px-6 text-center text-red-500">Gagal memuat data: ${error.message}</td></tr>`;
                } finally {
                    loadingIndicator.classList.add('hidden');
                }
            }

            // Muat data presensi untuk tanggal hari ini saat halaman pertama kali dimuat
            loadPresensiData(tanggalFilter.value);

            // Tambahkan event listener untuk tombol filter
            btnFilter.addEventListener('click', function() {
                loadPresensiData(tanggalFilter.value);
            });

            // Opsional: Muat data saat tanggal berubah (tanpa perlu klik tombol)
            // tanggalFilter.addEventListener('change', function() {
            //     loadPresensiData(tanggalFilter.value);
            // });
        });
    </script>

</body>
</html>
