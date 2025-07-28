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
    <title>Kelola Pengembalian - Admin LPSKE</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Verdana', sans-serif;;
            background-color: #f8fafc;
        }

        .btn {
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-weight: 600;
            color: white;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: transform 0.2s ease-in-out, background-color 0.2s ease-in-out;
            transform: scale(1);
        }

        .btn:hover {
            transform: scale(1.02);
        }

        .btn-secondary {
            background-color: #4b5563;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #374151;
        }

        .modal-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            z-index: 40;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: white;
            padding: 2rem;
            border-radius: 0.75rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            width: 90%;
            max-width: 500px;
            z-index: 50;
        }
    </style>
</head>

<body class="flex flex-col h-screen bg-gray-100 font-sans">
    

    <main class="flex-1 p-4 md:p-8 overflow-y-auto bg-gray-50">
        <div class="container mx-auto">
            <a href="dashboard.php"
                class="text-blue-600 hover:text-blue-800 transition duration-300 ease-in-out text-lg flex items-center mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                        clip-rule="evenodd" />
                </svg>
                Kembali Ke Dashboard
            </a>


            <div id="pengembalian-admin">
                <h2 class="text-3xl font-bold text-slate-800 mb-6">Daftar Peminjaman Aktif</h2>
                <p class="text-slate-600 mb-4">Berikut adalah daftar alat yang sedang dipinjam. Admin dapat mengklik
                    tombol "Kembalikan" untuk memproses pengembalian.</p>

                <div class="bg-white p-4 rounded-lg shadow-md overflow-x-auto">
                    <table class="min-w-full text-left table-auto">
                        <thead>
                            <tr class="bg-slate-100 text-slate-700 uppercase text-sm leading-normal">
                                <th class="py-3 px-6 text-left">Nama Alat</th>
                                <th class="py-3 px-6 text-left">Peminjam</th>
                                <th class="py-3 px-6 text-left">Keperluan</th>
                                <th class="py-3 px-6 text-left">Status</th>
                                <th class="py-3 px-6 text-left">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="pengembalian-table" class="text-slate-600 text-base font-normal"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <div id="custom-modal" class="modal-backdrop hidden">
        <div class="modal-content">
            <h3 id="modal-title" class="text-xl font-bold mb-4 text-slate-800"></h3>
            <p id="modal-body" class="text-slate-700 mb-6"></p>
            <div class="flex justify-end space-x-2">
                <button id="modal-close-btn" class="btn btn-secondary">Tutup</button>
            </div>
        </div>
    </div>

    <script>
        // Sesuaikan path API karena sekarang berada di folder admin/
        const API_BASE_URL = '../api/';

        function loadPengembalian() {
            fetch(API_BASE_URL + "get_pinjam.php")
                .then(res => res.json())
                .then(data => {
                    const table = document.getElementById("pengembalian-table");
                    table.innerHTML = "";

                    if (data.status === "error") {
                        table.innerHTML = `<tr><td colspan="5" class="p-3 text-center text-red-500">${data.pesan || "Gagal memuat data peminjaman aktif."}</td></tr>`;
                        return;
                    }
                    if (data.length === 0) {
                        table.innerHTML = `<tr><td colspan="5" class="p-3 text-center text-slate-500">Tidak ada data peminjaman aktif.</td></tr>`;
                        return;
                    }

                    data.forEach(item => {
                        const row = document.createElement("tr");
                        row.classList.add("border-b", "last:border-b-0", "hover:bg-slate-50");
                        row.innerHTML = `
                            <td class="py-2 px-6">${item.nama_alat}</td>
                            <td class="py-2 px-6">${item.nama_peminjam}</td>
                            <td class="py-2 px-6">${item.keperluan}</td>
                            <td class="py-2 px-6">
                                <span class="px-3 py-1 rounded-full text-xs font-medium ${item.status === 'Diajukan' ? 'bg-yellow-100 text-yellow-700' : (item.status === 'Dikembalikan' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700')}">${item.status}</span>
                            </td>
                            <td class="py-2 px-6">
                                <button onclick="kembalikanAlat(${item.id_pinjam})" class="px-3 py-1 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">Kembalikan</button>
                            </td>
                        `;
                        table.appendChild(row);
                    });
                })
                .catch(err => {
                    console.error("Gagal load pengembalian:", err);
                    document.getElementById("pengembalian-table").innerHTML = `<tr><td colspan="5" class="p-3 text-center text-red-500">Gagal memuat data pengembalian. Periksa koneksi atau server.</td></tr>`;
                });
        }

        function kembalikanAlat(id_pinjam) {
            if (!confirm("Apakah Anda yakin ingin mengembalikan alat ini?")) {
                return;
            }

            fetch(API_BASE_URL + "kembalikan_alat.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ id_pinjam: id_pinjam })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.status === "success") {
                        showModal("Sukses", data.pesan);
                        loadPengembalian(); // Refresh pengembalian list
                    } else {
                        showModal("Gagal", data.pesan);
                    }
                })
                .catch(err => {
                    console.error("Gagal mengembalikan alat:", err);
                    showModal("Error", "Terjadi kesalahan saat mengembalikan alat. Cek console untuk detail.");
                });
        }

        // Custom Modal Functions
        function showModal(title, body) {
            document.getElementById("modal-title").textContent = title;
            document.getElementById("modal-body").textContent = body;
            document.getElementById("custom-modal").classList.remove("hidden");
        }

        document.getElementById("modal-close-btn").addEventListener("click", () => {
            document.getElementById("custom-modal").classList.add("hidden");
        });

        // Load pengembalian data when the page loads
        document.addEventListener("DOMContentLoaded", loadPengembalian);
    </script>
</body>

</html>