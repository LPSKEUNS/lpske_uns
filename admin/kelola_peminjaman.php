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
    <title>Kelola Peminjaman - Admin LPSKE</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
        }

        .form-input {
            width: 100%;
            padding: 0.5rem 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }

        .form-input:focus {
            outline: none;
            border-color: #0ea5e9;
            box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.2);
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

        .btn-primary {
            background-color: #0284c7;
        }

        .btn-primary:hover {
            background-color: #0369a1;
        }

        .btn-secondary {
            background-color: #4b5563;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #374151;
        }

        .card {
            background-color: white;
            padding: 1.5rem;
            border-radius: 0.75rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            transition: box-shadow 0.3s ease-in-out;
        }

        .card:hover {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
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

            <div id="peminjaman-admin">
                <h2 class="text-3xl font-bold text-slate-800 mb-6">Formulir Peminjaman Alat/Ruang</h2>
                <div class="card max-w-lg mx-auto">
                    <form id="form-peminjaman" class="space-y-4">
                        <div>
                            <label for="select-inventory" class="block text-slate-700 text-sm font-bold mb-2">Pilih
                                Alat/Ruang</label>
                            <select id="select-inventory" name="id_alat" class="form-input" required>
                                <option value="">-- Pilih Alat/Ruang --</option>
                            </select>
                        </div>
                        <div>
                            <label for="pinjam-nama" class="block text-slate-700 text-sm font-bold mb-2">Nama
                                Peminjam</label>
                            <input type="text" id="pinjam-nama" name="nama_peminjam" class="form-input" required>
                        </div>
                        <div>
                            <label for="pinjam-keperluan"
                                class="block text-slate-700 text-sm font-bold mb-2">Keperluan</label>
                            <textarea id="pinjam-keperluan" name="keperluan" class="form-input" rows="3"
                                required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-full">Ajukan Peminjaman</button>
                    </form>
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

        function loadInventoryOptions() {
            fetch(API_BASE_URL + "get_inventory.php")
                .then(res => res.json())
                .then(data => {
                    const select = document.getElementById("select-inventory");
                    select.innerHTML = `<option value="">-- Pilih Alat/Ruang --</option>`;

                    if (data.status === "error") {
                        const option = document.createElement("option");
                        option.value = "";
                        option.textContent = data.pesan;
                        select.appendChild(option);
                        return;
                    }

                    data.forEach(item => {
                        if (item.tersedia > 0) {
                            const option = document.createElement("option");
                            option.value = item.id_alat;
                            option.textContent = `${item.nama_alat} (${item.tersedia} tersedia)`;
                            select.appendChild(option);
                        }
                    });
                    if (data.length === 0 || data.filter(item => item.tersedia > 0).length === 0) {
                        const option = document.createElement("option");
                        option.value = "";
                        option.textContent = "Tidak ada alat tersedia untuk dipinjam.";
                        select.appendChild(option);
                    }
                })
                .catch(err => {
                    console.error("Gagal load opsi alat:", err);
                    const select = document.getElementById("select-inventory");
                    select.innerHTML = `<option value="">Gagal memuat opsi alat.</option>`;
                });
        }

        document.getElementById("form-peminjaman").addEventListener("submit", function (e) {
            e.preventDefault();

            const idAlat = document.getElementById("select-inventory").value;
            const namaPeminjam = document.getElementById("pinjam-nama").value;
            const keperluan = document.getElementById("pinjam-keperluan").value;

            if (!idAlat || !namaPeminjam || !keperluan) {
                showModal("Peringatan", "Harap lengkapi semua data peminjaman!");
                return;
            }

            fetch(API_BASE_URL + "ajukan_pinjam.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    id_alat: idAlat,
                    nama_peminjam: namaPeminjam,
                    keperluan: keperluan
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === "success") {
                        showModal("Sukses", data.pesan);
                        this.reset();
                        loadInventoryOptions(); // Refresh select options
                    } else {
                        showModal("Gagal", data.pesan);
                    }
                })
                .catch(error => {
                    console.error("Error saat mengirim data peminjaman:", error);
                    showModal("Error", "Terjadi kesalahan saat mengajukan peminjaman. Cek console untuk detail.");
                });
        });

        // Custom Modal Functions
        function showModal(title, body) {
            document.getElementById("modal-title").textContent = title;
            document.getElementById("modal-body").textContent = body;
            document.getElementById("custom-modal").classList.remove("hidden");
        }

        document.getElementById("modal-close-btn").addEventListener("click", () => {
            document.getElementById("custom-modal").classList.add("hidden");
        });

        // Load options when the page loads
        document.addEventListener("DOMContentLoaded", loadInventoryOptions);
    </script>
</body>

</html>