<?php
session_start();

// Keamanan: Pastikan asisten sudah login
if (!isset($_SESSION['id_asisten'])) {
    header('Location: login_asisten.php?error=Anda harus login terlebih dahulu');
    exit;
}

// Ambil data nama asisten dari session untuk ditampilkan
$nama_asisten = $_SESSION['nama_asisten'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Asisten</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Custom styles for finer control and animations */
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(to bottom right, #e0f2f7, #c5e7f0); /* Lighter, more vibrant gradient */
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        main {
            flex-grow: 1; /* Allow main content to grow and push footer down */
        }
        .tab-button {
            padding: 0.85rem 1.75rem; /* Slightly larger tabs */
            font-weight: 600;
            border-bottom: 4px solid transparent;
            transition: all 0.3s ease-in-out;
            cursor: pointer;
            color: #4b5563; /* Darker gray for inactive tabs */
            border-radius: 0.5rem 0.5rem 0 0; /* Rounded top corners */
            position: relative;
            z-index: 10; /* Ensure tabs are above the border-b */
        }
        .tab-button:hover {
            color: #0d9488; /* Teal hover color */
            border-bottom-color: #5eead4; /* Lighter teal on hover */
        }
        .tab-button.active {
            color: #0d9488; /* Active teal color */
            border-bottom-color: #0d9488; /* Active tab border color */
            background-color: #ffffff; /* White background for active tab */
            box-shadow: 0 -2px 8px rgba(0, 0, 0, 0.05); /* Subtle shadow for active tab */
        }
        .tab-content {
            display: none;
            animation: fadeIn 0.6s ease-out forwards; /* Smoother, slightly longer fade-in */
            padding-top: 1rem; /* Space between tabs and content */
        }
        .tab-content.active {
            display: block;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }
        /* Custom button styles for a more polished look */
        .btn-primary {
            @apply w-full py-3 font-semibold rounded-xl shadow-lg transition-all duration-300 ease-in-out tracking-wide; /* Slightly more rounded, bolder text, tracking */
        }
        .btn-primary:hover {
            @apply shadow-xl transform -translate-y-1; /* Lift effect on hover */
        }
        .btn-green {
            @apply bg-emerald-600 text-white; /* More vibrant green */
        }
        .btn-green:hover {
            @apply bg-emerald-700;
        }
        .btn-red {
            @apply bg-rose-600 text-white; /* More vibrant red */
        }
        .btn-red:hover {
            @apply bg-rose-700;
        }
        .btn-teal {
            @apply bg-teal-600 text-white; /* New primary color for logbook */
        }
        .btn-teal:hover {
            @apply bg-teal-700;
        }
        /* Card styling */
        .card {
            @apply bg-white p-8 rounded-2xl shadow-xl border border-slate-100 hover:shadow-2xl transition-shadow duration-300; /* More rounded, softer shadow */
        }
        /* Table styling */
        .table-container {
            border-radius: 1rem; /* More rounded corners for table container */
            overflow: hidden;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.08); /* Deeper shadow for table */
        }
        table {
            border-collapse: collapse;
        }
        th, td {
            padding: 1rem 1.75rem; /* More padding */
            border-bottom: 1px solid #f1f5f9; /* Lighter border */
        }
        thead th {
            background-color: #f8fafc; /* Very light header background */
            color: #475569;
            font-size: 0.9rem; /* Slightly larger font for header */
            text-transform: uppercase;
            letter-spacing: 0.075em; /* More letter spacing */
            font-weight: 700; /* Bolder header text */
        }
        tbody tr:last-child td {
            border-bottom: none;
        }
        tbody tr:hover {
            background-color: #fefefe; /* Even subtler hover effect */
        }
        /* Scrollbar styling for logbook list */
        #riwayat-logbook-list::-webkit-scrollbar {
            width: 8px;
        }
        #riwayat-logbook-list::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        #riwayat-logbook-list::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        #riwayat-logbook-list::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</head>
<body class="bg-slate-100">

<header class="bg-slate-800 text-white p-5 flex justify-between items-center shadow-2xl">
    <h1 class="text-3xl font-extrabold tracking-wide">Dashboard Asisten</h1>
    <div class="flex items-center">
        <span class="mr-4 text-lg">Selamat datang, <span class="font-bold text-teal-300"><?= htmlspecialchars($nama_asisten); ?></span>!</span>
        <a href="logout_asisten.php" class="bg-rose-600 hover:bg-rose-700 text-white font-bold py-2.5 px-6 rounded-lg shadow-md hover:shadow-xl transition-all duration-300">
            <i class="fas fa-sign-out-alt mr-2"></i> Logout
        </a>
    </div>
</header>

<main class="container mx-auto p-8 lg:p-12">
    <div class="mb-8 border-b border-slate-200 relative">
        <nav class="flex -mb-px" aria-label="Tabs">
            <button class="tab-button active" data-target="presensi-content">
                <i class="fas fa-clock mr-2"></i> Presensi
            </button>
            <button class="tab-button" data-target="logbook-content">
                <i class="fas fa-book mr-2"></i> Logbook
            </button>
        </nav>
    </div>

    <div id="presensi-content" class="tab-content active">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-1">
                <div class="card text-center">
                    <h2 class="text-3xl font-bold text-slate-800 mb-6">Aksi Presensi</h2>
                    <p class="text-lg text-slate-600 mb-2">Waktu Server:</p>
                    <p id="current-time" class="text-6xl font-extrabold text-slate-900 mb-8 tracking-tight"></p>
                    <div class="flex flex-col gap-5">
                        <button id="check-in-btn" class="btn-primary btn-green">
                            <i class="fas fa-sign-in-alt mr-2"></i> Check-In
                        </button>
                        <button id="check-out-btn" class="btn-primary btn-red">
                            <i class="fas fa-sign-out-alt mr-2"></i> Check-Out
                        </button>
                    </div>
                </div>
            </div>
            <div class="lg:col-span-2 card">
                <h2 class="text-3xl font-bold text-slate-800 mb-6">Riwayat Presensi Anda</h2>
                <div class="overflow-x-auto table-container">
                    <table class="min-w-full text-left table-auto">
                        <thead>
                            <tr>
                                <th class="py-3 px-6">Tanggal</th>
                                <th class="py-3 px-6">Masuk</th>
                                <th class="py-3 px-6">Keluar</th>
                                <th class="py-3 px-6">Status</th>
                            </tr>
                        </thead>
                        <tbody id="riwayat-presensi-table" class="text-slate-700 text-base">
                            <!-- Presensi data will be loaded here by JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div id="logbook-content" class="tab-content">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-1">
                <div class="card">
                    <h2 class="text-3xl font-bold text-slate-800 mb-6">Isi Logbook</h2>
                    <form id="form-logbook" class="space-y-5">
                        <div>
                            <label for="logbook-kegiatan" class="block text-slate-700 text-lg font-semibold mb-2">Catatan Kegiatan Hari Ini</label>
                            <textarea id="logbook-kegiatan" name="kegiatan" class="w-full p-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent transition-all duration-200" rows="8" required placeholder="Contoh: Melakukan kalibrasi osiloskop, membuat laporan mingguan, membantu mahasiswa praktikum..."></textarea>
                        </div>
                        <button type="submit" class="btn-primary btn-teal">
                            <i class="fas fa-save mr-2"></i> Simpan Catatan
                        </button>
                    </form>
                </div>
            </div>
            <div class="lg:col-span-2 card">
                <h2 class="text-3xl font-bold text-slate-800 mb-6">Riwayat Logbook Anda</h2>
                <div id="riwayat-logbook-list" class="space-y-4 max-h-96 overflow-y-auto pr-2">
                    <!-- Logbook data will be loaded here by JavaScript -->
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Custom Modal for Alerts -->
<div id="custom-modal" class="fixed inset-0 bg-gray-900 bg-opacity-70 flex items-center justify-center p-4 z-50 hidden">
    <div class="bg-white p-8 rounded-xl shadow-3xl max-w-sm w-full mx-auto transform transition-all duration-300 scale-90 opacity-0" id="modal-content-wrapper">
        <h3 id="modal-title" class="text-2xl font-bold text-slate-800 mb-4"></h3>
        <p id="modal-body" class="text-gray-700 mb-6 text-lg"></p>
        <div class="flex justify-end">
            <button id="modal-close-btn" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow-md hover:shadow-xl transition-all duration-300">
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
const API_BASE_URL = 'http://localhost/lpske/api/';

// Function to show the custom modal
function showModal(title, body) {
    document.getElementById('modal-title').textContent = title;
    document.getElementById('modal-body').textContent = body;
    const modal = document.getElementById('custom-modal');
    const modalContent = document.getElementById('modal-content-wrapper');

    modal.classList.remove('hidden');
    // Trigger fade-in and scale-up animation
    setTimeout(() => {
        modalContent.classList.remove('opacity-0', 'scale-90'); // Changed from scale-95
        modalContent.classList.add('opacity-100', 'scale-100');
    }, 10); // Small delay to ensure CSS transition applies
}

// Function to update the current time display
function updateTime() {
    const timeEl = document.getElementById("current-time");
    if (timeEl) {
        timeEl.textContent = new Date().toLocaleTimeString("id-ID", { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    }
}

// Function to load and display attendance history
function loadRiwayatPresensi() {
    fetch(API_BASE_URL + "get_riwayat_presensi.php", { credentials: "include" })
    .then(res => res.json())
    .then(data => {
        const tableBody = document.getElementById("riwayat-presensi-table");
        tableBody.innerHTML = ""; // Clear existing rows

        // Check for error status or empty array
        if (data.status === "error" || !Array.isArray(data) || data.length === 0) {
            tableBody.innerHTML = `<tr><td colspan="4" class="text-center p-4 text-slate-500 italic">Belum ada riwayat presensi.</td></tr>`;
            return;
        }

        // Populate table with attendance records
        data.forEach(log => {
            const row = document.createElement("tr");
            row.classList.add("border-b", "border-slate-100", "hover:bg-slate-50"); // Add subtle hover effect
            row.innerHTML = `
                <td class="py-3 px-6">${new Date(log.tanggal).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' })}</td>
                <td class="py-3 px-6">${log.jam_masuk}</td>
                <td class="py-3 px-6">${log.jam_keluar || '-'}</td>
                <td class="py-3 px-6">
                    <span class="px-3 py-1 rounded-full text-xs font-medium
                        ${log.status === 'Check-Out' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700'}">
                        ${log.status}
                    </span>
                </td>
            `;
            tableBody.appendChild(row);
        });
    })
    .catch(error => {
        console.error("Error loading presensi history:", error);
        showModal("Error", "Gagal memuat riwayat presensi. Silakan coba lagi.");
    });
}

// Function to load and display personal logbook history
function loadRiwayatLogbook() {
    fetch(API_BASE_URL + "get_riwayat_logbook_pribadi.php", { credentials: "include" })
    .then(res => res.json())
    .then(data => {
        const list = document.getElementById("riwayat-logbook-list");
        list.innerHTML = ""; // Clear existing items

        // Check for empty array
        if (!Array.isArray(data) || data.length === 0) {
            list.innerHTML = `<p class="text-slate-500 text-center italic p-4">Belum ada riwayat logbook.</p>`;
            return;
        }

        // Populate list with logbook entries
        data.forEach(log => {
            const item = document.createElement("div");
            item.className = "p-4 border-b border-slate-200 last:border-b-0 bg-slate-50 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200";
            item.innerHTML = `
                <p class="text-slate-800 text-base leading-relaxed">${log.kegiatan}</p>
                <p class="text-xs text-slate-500 mt-2 font-light">
                    <i class="far fa-calendar-alt mr-1"></i>
                    ${new Date(log.tanggal).toLocaleString('id-ID', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' })}
                </p>
            `;
            list.appendChild(item);
        });
    })
    .catch(error => {
        console.error("Error loading logbook history:", error);
        showModal("Error", "Gagal memuat riwayat logbook. Silakan coba lagi.");
    });
}

// Event listener for DOM content loaded
document.addEventListener("DOMContentLoaded", function() {
    // Update time every second
    setInterval(updateTime, 1000);
    updateTime(); // Initial call to display time immediately

    // Load data when the page first opens
    loadRiwayatPresensi();
    loadRiwayatLogbook();

    // Tab switching logic
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');

    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            // Remove active class from all buttons and contents
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));

            // Add active class to the clicked button and its corresponding content
            button.classList.add('active');
            document.getElementById(button.dataset.target).classList.add('active');
        });
    });

    // Close modal button event listener
    document.getElementById('modal-close-btn').addEventListener('click', () => {
        const modal = document.getElementById('custom-modal');
        const modalContent = document.getElementById('modal-content-wrapper');

        // Trigger fade-out and scale-down animation
        modalContent.classList.remove('opacity-100', 'scale-100');
        modalContent.classList.add('opacity-0', 'scale-90');

        // Hide modal after animation completes
        modalContent.addEventListener('transitionend', function handler() {
            modal.classList.add('hidden');
            modalContent.removeEventListener('transitionend', handler); // Clean up listener
        });
    });

    // Check-in button event listener
    document.getElementById("check-in-btn").addEventListener("click", () => {
        fetch(API_BASE_URL + "check_in.php", { method: "POST", credentials: "include" })
        .then(res => res.json())
        .then(data => {
            showModal(data.status === "success" ? "Sukses" : "Gagal", data.pesan);
            if (data.status === "success") {
                loadRiwayatPresensi(); // Reload attendance history on success
            }
        })
        .catch(error => {
            console.error("Error during check-in:", error);
            showModal("Error", "Terjadi kesalahan saat check-in. Silakan coba lagi.");
        });
    });

    // Check-out button event listener
    document.getElementById("check-out-btn").addEventListener("click", () => {
        fetch(API_BASE_URL + "check_out.php", { method: "POST", credentials: "include" })
        .then(res => res.json())
        .then(data => {
            showModal(data.status === "success" ? "Sukses" : "Gagal", data.pesan);
            if (data.status === "success") {
                loadRiwayatPresensi(); // Reload attendance history on success
            }
        })
        .catch(error => {
            console.error("Error during check-out:", error);
            showModal("Error", "Terjadi kesalahan saat check-out. Silakan coba lagi.");
        });
    });

    // Logbook form submission handler
    document.getElementById("form-logbook").addEventListener("submit", function(e) {
        e.preventDefault(); // Prevent default form submission
        const kegiatan = document.getElementById("logbook-kegiatan").value;

        fetch(API_BASE_URL + "submit_logbook.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            credentials: "include",
            body: JSON.stringify({ kegiatan: kegiatan })
        })
        .then(res => res.json())
        .then(data => {
            showModal(data.status === "success" ? "Sukses" : "Gagal", data.pesan);
            if(data.status === "success") {
                this.reset(); // Clear form on success
                loadRiwayatLogbook(); // Reload logbook history on success
            }
        })
        .catch(error => {
            console.error("Error submitting logbook:", error);
            showModal("Error", "Terjadi kesalahan saat menyimpan catatan logbook. Silakan coba lagi.");
        });
    });
});
</script>

</body>
</html>
