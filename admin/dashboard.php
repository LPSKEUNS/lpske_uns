<?php
// GATEKEEPER / PENJAGA
// Selalu mulai dengan session_start() di baris paling atas
session_start();

// Periksa apakah session 'admin_loggedin' tidak ada atau tidak bernilai true
if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    // Jika tidak login, tendang pengguna ke halaman login
    header('Location: ../login_admin.php'); // ../ karena kita keluar dari folder 'admin'
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" xintegrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        /* Menggunakan font Inter sebagai dasar */
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc; /* Latar belakang abu-abu sangat terang */
            color: #334155; 
        }

        /* Container utama dengan bayangan yang lebih halus */
        .dashboard-container {
            max-width: 1100px;
            width: 100%;
            background-color: #ffffff;
            border-radius: 1.5rem; /* 24px */
            padding: 2.5rem 3rem;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.05);
            border: 1px solid #f1f5f9;
        }

        /* Header dengan tipografi yang lebih tegas */
        .header-section h1 {
            font-weight: 700;
            color: #1e293b; 
        }
        
        .header-section strong {
            font-weight: 600;
            color: #0ea5e9; /* sky-500 */
        }

        /* Judul menu yang lebih bersih */
        .menu-title {
            font-weight: 700;
            color: #1e293b;
            text-align: center;
            margin-bottom: 2rem;
        }

        /* Kartu menu dengan gaya baru yang lebih soft */
        .dashboard-card {
            position: relative;
            display: block;
            padding: 1.5rem;
            border-radius: 0.75rem; /* 12px */
            transition: all 0.2s ease-in-out;
            text-align: left;
            overflow: hidden; /* Untuk menjaga ikon tetap di dalam batas kartu */
            border: 1px solid #e2e8f0;
        }
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            border-color: #cbd5e1;
        }
        .dashboard-card h3 {
            font-weight: 700;
            font-size: 1.125rem; /* 18px */
            margin-bottom: 0.25rem;
        }
        .dashboard-card p {
            font-size: 0.875rem; /* 14px */
            opacity: 0.9;
        }
        .card-icon {
            position: absolute;
            top: -10px;
            right: -10px;
            font-size: 4rem; /* 64px */
            opacity: 0.1;
            transform: rotate(-15deg);
        }
        
        /* Warna spesifik untuk setiap kartu */
        .card-blue { background-color: #eff6ff; color: #1d4ed8; }
        .card-green { background-color: #f0fdf4; color: #166534; }
        .card-purple { background-color: #f5f3ff; color: #5b21b6; }
        .card-yellow { background-color: #fefce8; color: #854d0e; }
        .card-teal { background-color: #f0fdfa; color: #0f766e; }
        .card-orange { background-color: #fff7ed; color: #9a3412; }

        /* Penyesuaian responsif */
        @media (max-width: 768px) {
            .dashboard-container {
                padding: 2rem 1.5rem;
            }
            .header-section {
                flex-direction: column;
                text-align: center;
            }
            .header-section div {
                margin-bottom: 1.5rem;
            }
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4 md:p-6">
    <div class="dashboard-container">
        <!-- Header Section -->
        <div class="header-section flex flex-wrap justify-between items-center pb-6 mb-6 border-b border-gray-200">
            <div class="mb-4 md:mb-0">
                <h1 class="text-3xl md:text-4xl">Selamat Datang, Admin!</h1>
                <p class="mt-2 text-md text-slate-600">Anda login sebagai: <strong><?php echo htmlspecialchars($_SESSION['admin_username']); ?></strong></p>
                <p class="mt-1 text-slate-500">Kelola sistem Anda dengan mudah dari sini.</p>
            </div>
            <a href="../logout_admin.php" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-6 rounded-lg transition-colors">
                Logout
            </a>
        </div>

        <!-- Menu Manajemen -->
        <div>
            <h2 class="menu-title text-2xl md:text-3xl mt-4">Menu Manajemen</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 md:gap-6">
                
                <a href="kelola_dosen.php" class="dashboard-card card-blue">
                    <i class="fas fa-chalkboard-teacher card-icon"></i>
                    <h3>Dosen</h3>
                    <p>Kelola data dosen.</p>
                </a>

                <a href="kelola_asisten.php" class="dashboard-card card-green">
                    <i class="fas fa-users card-icon"></i>
                    <h3>Asisten</h3>
                    <p>Kelola data asisten.</p>
                </a>

                <a href="kelola_inventaris.php" class="dashboard-card card-purple">
                    <i class="fas fa-boxes-stacked card-icon"></i>
                    <h3>Inventaris</h3>
                    <p>Pantau dan kelola inventaris.</p>
                </a>

                <a href="persetujuan_izin.php" class="dashboard-card card-yellow">
                    <i class="fas fa-file-signature card-icon"></i>
                    <h3>Izin</h3>
                    <p>Tinjau dan setujui perizinan.</p>
                </a>

                <a href="kelola_peminjaman.php" class="dashboard-card card-teal">
                    <i class="fas fa-hand-holding-box card-icon"></i>
                    <h3>Peminjaman</h3>
                    <p>Catat peminjaman alat.</p>
                </a>

                <a href="kelola_pengembalian.php" class="dashboard-card card-orange">
                    <i class="fas fa-box-check card-icon"></i>
                    <h3>Pengembalian</h3>
                    <p>Proses pengembalian alat.</p>
                </a>

                <a href="kelola_presensi.php" class="dashboard-card card-green">
                    <i class="fas fa-box-check card-icon"></i>
                    <h3>Presensi Asisten</h3>
                    <p>Cek.</p>
                </a>

            </div>
        </div>
    </div>
</body>
</html>
